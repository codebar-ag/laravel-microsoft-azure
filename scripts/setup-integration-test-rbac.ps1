#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Grants this package's integration-test service principal Contributor access on the target
    Azure subscription, so `composer test:live` / `composer test:record` stop skipping with
    "Azure RBAC: Assign Contributor (or equivalent) on MICROSOFT_AZURE_SUBSCRIPTION_ID".

.DESCRIPTION
    tests/Pest.php's `runAzureIntegration()` skips a test (rather than failing it) whenever Azure
    returns a 403 that looks like a missing role assignment. This script grants the Contributor
    role on the subscription to the service principal so those tests can run for real instead.

    Run this as a principal that already has Owner or User Access Administrator on the
    subscription — the test service principal itself does not have rights to grant itself access.

.PARAMETER TenantId
    Defaults to $env:MICROSOFT_AZURE_TENANT_ID (same value used by phpunit.xml).

.PARAMETER SubscriptionId
    Defaults to $env:MICROSOFT_AZURE_SUBSCRIPTION_ID.

.PARAMETER ServicePrincipalObjectId
    Entra ID object id (not the app/client id) of the test service principal. Defaults to
    $env:MICROSOFT_AZURE_SERVICE_PRINCIPAL_OBJECT_ID. If neither is available, pass
    -ServicePrincipalAppId instead and it will be resolved for you.

.PARAMETER ServicePrincipalAppId
    App (client) id, used only to resolve the object id when -ServicePrincipalObjectId isn't
    supplied. Defaults to $env:MICROSOFT_AZURE_CLIENT_ID.

.PARAMETER RoleDefinitionName
    ARM role to grant on the subscription. Defaults to 'Contributor'.

.EXAMPLE
    ./scripts/setup-integration-test-rbac.ps1
#>

param(
    [string]$TenantId = $env:MICROSOFT_AZURE_TENANT_ID,
    [string]$SubscriptionId = $env:MICROSOFT_AZURE_SUBSCRIPTION_ID,
    [string]$ServicePrincipalObjectId = $env:MICROSOFT_AZURE_SERVICE_PRINCIPAL_OBJECT_ID,
    [string]$ServicePrincipalAppId = $env:MICROSOFT_AZURE_CLIENT_ID,
    [string]$RoleDefinitionName = 'Contributor'
)

$ErrorActionPreference = 'Stop'

if (-not (Get-Module -ListAvailable -Name Az.Accounts)) {
    throw "The Az PowerShell module isn't installed. Run: Install-Module -Name Az -Scope CurrentUser"
}

if (-not $SubscriptionId) {
    throw 'SubscriptionId is required (pass -SubscriptionId or set MICROSOFT_AZURE_SUBSCRIPTION_ID).'
}

if (-not $ServicePrincipalObjectId -and -not $ServicePrincipalAppId) {
    throw 'Provide -ServicePrincipalObjectId or -ServicePrincipalAppId (or set the matching env var).'
}

Write-Host "Connecting to Azure..." -ForegroundColor Cyan
if ($TenantId) {
    Connect-AzAccount -Tenant $TenantId | Out-Null
} else {
    Connect-AzAccount | Out-Null
}

Set-AzContext -Subscription $SubscriptionId | Out-Null

if (-not $ServicePrincipalObjectId) {
    Write-Host "Resolving service principal object id from app id $ServicePrincipalAppId..." -ForegroundColor Cyan
    $sp = Get-AzADServicePrincipal -ApplicationId $ServicePrincipalAppId
    if (-not $sp) {
        throw "No service principal found for app id $ServicePrincipalAppId."
    }
    $ServicePrincipalObjectId = $sp.Id
}

$subscriptionScope = "/subscriptions/$SubscriptionId"

Write-Host "Checking existing '$RoleDefinitionName' assignment on $subscriptionScope..." -ForegroundColor Cyan
$existing = Get-AzRoleAssignment -ObjectId $ServicePrincipalObjectId -RoleDefinitionName $RoleDefinitionName -Scope $subscriptionScope -ErrorAction SilentlyContinue

if ($existing) {
    Write-Host "Already assigned — nothing to do." -ForegroundColor Green
} else {
    Write-Host "Granting '$RoleDefinitionName' on $subscriptionScope..." -ForegroundColor Cyan
    New-AzRoleAssignment -ObjectId $ServicePrincipalObjectId -RoleDefinitionName $RoleDefinitionName -Scope $subscriptionScope | Out-Null
    Write-Host "Granted." -ForegroundColor Green
}

Write-Host "Done." -ForegroundColor Cyan
