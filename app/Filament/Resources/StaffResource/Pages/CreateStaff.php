<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Plan;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected static bool $canCreateAnother = false;
    protected function getCreatedNotificationTitle(): ?string
    {
        return __('messages.placeholder.staff_created_successfully');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $data['password'] = Hash::make($data['password']);
            $data['status'] = !empty($data['status']) ? Staff::ACTIVE : Staff::DEACTIVE;
            $data['type'] = User::STAFF;
            if (Schema::hasColumn('users', 'is_default')) {
                $data['is_default'] = false;
            }
            $staff = User::create($data);

            // if (isset($data['roles']) && !empty($data['roles'])) {
                $role = Role::where('id', $data['roles'])->first();
                $staff->assignRole($role->name);
            // }
            if ($staff->hasRole('customer')) {
                $plan = Plan::whereIsDefault(true)->first();
                Subscription::create([
                    'plan_id' => $plan['id'],
                    'plan_amount' => $plan['price'],
                    'payable_amount' => $plan['price'],
                    'plan_frequency' => Plan::MONTHLY,
                    'starts_at' => Carbon::now(),
                    'ends_at' => Carbon::now()->addDays($plan['trial_days']),
                    'trial_ends_at' => Carbon::now()->addDays($plan['trial_days']),
                    'status' => Subscription::ACTIVE,
                    'user_id' => $staff['id'],
                    'no_of_post' => $plan['post_count'],
                ]);
            }
            $staff->sendEmailVerificationNotification();

            DB::commit();

            return $staff;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    // change create another button lable
    protected function getCreateAnotherButtonLabel(): string
    {
        return __('messages.placeholder.create_another_staff');
    }
}
