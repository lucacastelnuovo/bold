<div class="bg-white rounded-lg shadow-md p-6 w-full max-w-md">
    <div class="text-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">{{ $this->guest }}</h1>
        <p class="text-gray-600">Toegang verloopt over: {{ $this->expires->diffForHumans() }}</p>
    </div>

    <ul class="divide-y divide-gray-100 overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        @forelse ($this->locks as $lock)
            <li class="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 cursor-pointer"
                wire:click="activate({{ $lock }})">

                <div class="min-w-0 flex-auto">
                    <p class="text-sm/6 text-center font-semibold text-gray-900">
                        Click to open: {{ $lock->bold_name }}
                    </p>
                </div>

            </li>
        @empty
            <li class="relative flex justify-between gap-x-6 px-4 py-5 sm:px-6">

                <div class="min-w-0 flex-auto">
                    <p class="text-sm/6 text-center font-semibold text-red-900">
                        No locks found!
                    </p>
                </div>

            </li>
        @endforelse
    </ul>
</div>
