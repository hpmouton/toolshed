<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
| All Pest tests in the Feature and Unit directories will use the Laravel
| TestCase with RefreshDatabase so every test starts with a clean slate.
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');
