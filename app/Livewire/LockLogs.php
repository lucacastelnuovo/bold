<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

class LockLogs extends Component
{
    #[Locked]
    public Collection $activities;

    public function mount()
    {
        // get this from the user, auth()->user()->activities
        $this->activities = Activity::latest()
            ->with('causer')
            ->take(10)
            ->get();
    }

    public function render()
    {
        return <<<'HTML'
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left ">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-1/4">
                                {{ __('Datum') }}
                            </th>
                            <th scope="col" class="px-6 py-3 w-1/4">
                                {{ __('Eigenaar') }}
                            </th>
                            <th scope="col" class="px-6 py-3">
                                {{ __('Omschrijving') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->activities as $activity)
                            <tr class="bg-white border-b">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $activity->created_at->format('d F Y · H:i') }}
                                </th>
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $activity->causer?->name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $activity->description }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            HTML;
    }
}
