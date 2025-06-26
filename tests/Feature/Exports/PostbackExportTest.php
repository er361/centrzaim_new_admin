<?php

use App\Exports\PostbackExport;
use App\Models\Action;
use App\Models\Source;
use App\Models\User;
use App\Models\Postback;
use App\Models\Webmaster;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

it('includes transaction_id in mapped data', function () {
    // Clean up tables
    Postback::query()->delete();
    User::query()->delete();
    Action::query()->delete();
    Webmaster::query()->delete();
    Source::query()->delete();
    // Create Source
    $source = Source::create([
        'id' => 1,
        'name' => 'Test Source',
        'is_active' => true,
    ]);

    // Create Webmaster
    $webmaster = Webmaster::create([
        'source_id' => $source->id,
        'api_id' => 'test_webmaster_123',
        'income_percent' => 0.5,
    ]);

    // Create User
    $user = User::create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'webmaster_id' => $webmaster->id,
        'transaction_id' => 'test_transaction_123',
        'name' => 'Test',
        'last_name' => 'User',
        'phone' => '+71234567890',
    ]);

    // Create Action with cookie fields
    $action = Action::create([
        'webmaster_id' => $webmaster->id,
        'api_transaction_id' => 'test_transaction_123',
        'ip' => '127.0.0.1',
        'user_agent' => 'Test User Agent',
        'site_id' => 'site_123',
        'place_id' => 'place_456',
        'banner_id' => 'banner_789',
        'campaign_id' => 'campaign_999',
    ]);

    // Create Postback
    $postback = Postback::create([
        'user_id' => $user->id,
        'remote_user_id' => 'remote_123',
        'cost' => 100.50,
        'sent_at' => null,
    ]);

    // Create PostbackExport instance
    $query = Postback::query();
    $export = new PostbackExport($query);

    // Load relations for postback
    $postback->load(['user.webmaster.source', 'user.action']);

    $fileName = 'postbacks_' . Carbon::now()->format('Ymd_His') . '.xlsx';

    // Store the export file
    Excel::store(new PostbackExport($query), $fileName);
    
    // Check that file was created
    expect(Storage::exists($fileName))->toBeTrue();
});