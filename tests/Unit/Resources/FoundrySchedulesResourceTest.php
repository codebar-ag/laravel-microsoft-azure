<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\CreateOrUpdateSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\DeleteSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetScheduleRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListScheduleRuns;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListSchedules;
use CodebarAg\MicrosoftAzure\Resources\FoundryScheduleRunsResource;
use CodebarAg\MicrosoftAzure\Resources\FoundrySchedulesResource;
use Saloon\Http\Faking\MockResponse;

it('creates or updates, lists, gets and deletes schedules', function (): void {
    $client = clientWithFoundryMock([
        CreateOrUpdateSchedule::class => MockResponse::make(body: ['id' => 'daily-digest']),
        ListSchedules::class => MockResponse::make(body: ['data' => [['id' => 'daily-digest']]]),
        GetSchedule::class => MockResponse::make(body: ['id' => 'daily-digest', 'cron' => '0 8 * * *']),
        DeleteSchedule::class => MockResponse::make(status: 204),
    ]);

    $schedules = $client->foundry('my-foundry', 'default')->schedules();

    expect($schedules)->toBeInstanceOf(FoundrySchedulesResource::class)
        ->and($schedules->createOrUpdate('daily-digest', ['cron' => '0 8 * * *']))->toHaveKey('id', 'daily-digest')
        ->and($schedules->list())->toHaveCount(1)
        ->and($schedules->get('daily-digest'))->toHaveKey('cron', '0 8 * * *');

    $schedules->delete('daily-digest');
});

it('lists and gets schedule runs via the nested runs gateway', function (): void {
    $client = clientWithFoundryMock([
        ListScheduleRuns::class => MockResponse::make(body: ['data' => [['id' => 'run-1']]]),
        GetScheduleRun::class => MockResponse::make(body: ['id' => 'run-1', 'status' => 'succeeded']),
    ]);

    $runs = $client->foundry('my-foundry', 'default')->schedules()->runs('daily-digest');

    expect($runs)->toBeInstanceOf(FoundryScheduleRunsResource::class)
        ->and($runs->list())->toHaveCount(1)
        ->and($runs->get('run-1'))->toHaveKey('status', 'succeeded');
});
