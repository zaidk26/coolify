@props([
'title' => 'Are you sure?',
'isErrorButton' => false,
'buttonTitle' => 'Confirm Action',
'buttonFullWidth' => false,
'customButton' => null,
'disabled' => false,
'action' => 'delete',
'content' => null,
'checkboxes' => [],
'checkboxActions' => [],
'actions' => [],
'confirmWithText' => true,
'confirmText' => 'Confirm Deletion',
'confirmWithPassword' => true,
'step1ButtonText' => 'Continue Deletion',
'step2ButtonText' => 'Delete Permanently',
'step3ButtonText' => 'Confirm Permanent Deletion',
])

<div x-data="{
    modalOpen: false,
    step: {{ !empty($checkboxes) ? 1 : ($confirmWithPassword ? 2 : 3) }},
    initialStep: {{ !empty($checkboxes) ? 1 : ($confirmWithPassword ? 2 : 3) }},
    finalStep: {{ $confirmWithPassword ? 3 : (!empty($checkboxes) || $confirmWithText ? 2 : 1) }},
    selectedActions: @js(collect($checkboxes)->where('model', true)->pluck('id')->toArray()),
    deleteText: '',
    password: '',
    checkboxActions: @js($checkboxActions),
    actions: @js($actions),
    confirmText: @js($confirmText),
    userConfirmText: '',
    confirmWithText: @js($confirmWithText),
    confirmWithPassword: @js($confirmWithPassword),
    copied: false,
    action: @js($action),
    getActionText(action) {
        return this.checkboxActions[action] || action;
    },
    resetModal() {
        this.step = this.initialStep;
        this.selectedActions = @js(collect($checkboxes)->where('model', true)->pluck('id')->toArray());
        this.deleteText = '';
        this.password = '';
        this.userConfirmText = '';
    },
    step1ButtonText: @js($step1ButtonText),
    step2ButtonText: @js($step2ButtonText),
    step3ButtonText: @js($step3ButtonText),
    executeAction() {
        if (this.confirmWithPassword) {
            $wire.call(this.action, this.password);
        } else {
            $wire.call(this.action);
        }
        this.modalOpen = false;
        this.resetModal();
    },
    copyConfirmText() {
        navigator.clipboard.writeText(this.confirmText);
        this.copied = true;
        setTimeout(() => {
            this.copied = false;
        }, 2000);
    }
}" @keydown.escape.window="modalOpen = false; resetModal()" :class="{ 'z-40': modalOpen }" class="relative w-auto h-auto">
    @if ($customButton)
    @if ($buttonFullWidth)
    <x-forms.button @click="modalOpen=true" class="w-full">
        {{ $customButton }}
    </x-forms.button>
    @else
    <x-forms.button @click="modalOpen=true">
        {{ $customButton }}
    </x-forms.button>
    @endif
    @else
    @if ($content)
    <div @click="modalOpen=true">
        {{ $content }}
    </div>
    @else
    @if ($disabled)
    @if ($buttonFullWidth)
    <x-forms.button class="w-full" isError disabled wire:target>
        {{ $buttonTitle }}
    </x-forms.button>
    @else
    <x-forms.button isError disabled wire:target>
        {{ $buttonTitle }}
    </x-forms.button>
    @endif
    @elseif ($isErrorButton)
    @if ($buttonFullWidth)
    <x-forms.button class="w-full" isError @click="modalOpen=true">
        {{ $buttonTitle }}
    </x-forms.button>
    @else
    <x-forms.button isError @click="modalOpen=true">
        {{ $buttonTitle }}
    </x-forms.button>
    @endif
    @else
    @if ($buttonFullWidth)
    <x-forms.button @click="modalOpen=true" class="flex w-full gap-2" wire:target>
        {{ $buttonTitle }}
    </x-forms.button>
    @else
    <x-forms.button @click="modalOpen=true" class="flex gap-2" wire:target>
        {{ $buttonTitle }}
    </x-forms.button>
    @endif
    @endif
    @endif
    @endif
    <template x-teleport="body">
        <div x-show="modalOpen" 
             @click.away="modalOpen = false; resetModal()"
             class="fixed top-0 lg:pt-10 left-0 z-[99] flex items-start justify-center w-screen h-screen" 
             x-cloak>
            <div x-show="modalOpen" 
                 x-transition:enter="ease-out duration-100" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-100" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 @click="modalOpen = false; resetModal()" 
                 class="absolute inset-0 w-full h-full bg-black bg-opacity-20 backdrop-blur-sm"></div>
            <div x-show="modalOpen" 
                 x-trap.inert.noscroll="modalOpen" 
                 x-transition:enter="ease-out duration-100" 
                 x-transition:enter-start="opacity-0 -translate-y-2 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-100" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 -translate-y-2 sm:scale-95" 
                 class="relative w-full py-6 border rounded min-w-full lg:min-w-[36rem] max-w-fit bg-neutral-100 border-neutral-400 dark:bg-base px-7 dark:border-coolgray-300">
                <div class="flex items-center justify-between pb-3">
                    <h3 class="text-2xl font-bold">{{ $title }}</h3>
                    <button @click="modalOpen = false; resetModal()"
                        class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 mt-5 mr-5 rounded-full dark:text-white hover:bg-coolgray-300">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="relative w-auto pb-8">
                    @if(!empty($checkboxes))
                    <!-- Step 1: Select actions -->
                    <div x-show="step === 1">
                        <div class="flex justify-between items-center mb-4">
                            <div class="px-2">Select the actions you want to perform:</div>
                        </div>
                        @foreach($checkboxes as $index => $checkbox)
                        <x-forms.checkbox 
                            :id="$checkbox['id']" 
                            :wire:model="$checkbox['model']" 
                            :label="$checkbox['label']" 
                            x-on:change="$event.target.checked ? (selectedActions.includes('{{ $checkbox['id'] }}') || selectedActions.push('{{ $checkbox['id'] }}')) : selectedActions = selectedActions.filter(a => a !== '{{ $checkbox['id'] }}')"
                            :checked="$checkbox['model']"
                        ></x-forms.checkbox>
                        @endforeach
                    </div>
                    @endif

                    <!-- Step 2: Confirm deletion -->
                    <div x-show="step === 2 || (!confirmWithPassword && step === 3)">
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Warning</p>
                            <p>This operation is not reversible. Please proceed with caution.</p>
                        </div>
                        <div class="px-2 mb-4">The following action<span x-show="actions.length + selectedActions.length > 1">s</span> will be performed:</div>
                        <ul class="mb-4 space-y-2">
                            <template x-for="action in actions" :key="action">
                                <li class="flex items-center text-red-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <span x-text="action" class="font-bold"></span>
                                </li>
                            </template>
                            <template x-if="selectedActions.length > 0">
                                <template x-for="action in selectedActions" :key="action">
                                    <li class="flex items-center text-red-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span x-text="checkboxActions[action]" class="font-bold"></span>
                                    </li>
                                </template>
                            </template>
                        </ul>
                        @if($confirmWithText)
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold mb-2">Confirm Actions</h4>
                            <p class="text-sm mb-2">Please confirm the execution of the actions by entering the Resource Name below</p>
                            
                            <div class="relative mb-2">
                                <input 
                                    type="text" 
                                    x-model="confirmText" 
                                    class="w-full p-2 pr-10 rounded text-black input cursor-text"
                                    readonly
                                >
                                <button 
                                    @click="copyConfirmText()"
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                                    title="Copy confirmation text"
                                    x-ref="copyButton"
                                >
                                    <template x-if="!copied">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                            <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                                        </svg>
                                    </template>
                                    <template x-if="copied">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </template>
                                </button>
                            </div>
                            
                            <label for="userConfirmText" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mt-4">
                                Resource Name
                            </label>
                            <input 
                                type="text" 
                                x-model="userConfirmText" 
                                class="w-full p-2 rounded text-black input mt-1"
                                placeholder="Type the Resource Name here"
                            >
                        </div>
                        @endif
                    </div>

                    <!-- Step 3: Password confirmation -->
                    @if($confirmWithPassword)
                    <div x-show="step === 3">
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">Final Confirmation</p>
                            <p>Please enter your password to confirm this destructive action.</p>
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Your Password
                            </label>
                            <input type="password" id="password-confirm" x-model="password" class="input" placeholder="Enter your password">
                        </div>
                    </div>
                    @endif
                </div>
                <!-- Navigation buttons -->
                <div class="flex flex-row justify-between mt-4">
                    <template x-if="step > initialStep">
                        <x-forms.button 
                            @click="step--" 
                            class="w-24 dark:bg-coolgray-200 dark:hover:bg-coolgray-300"
                        >
                            Back
                        </x-forms.button>
                    </template>
                    <template x-if="step === initialStep">
                        <x-forms.button 
                            @click="modalOpen = false; resetModal()" 
                            class="w-24 dark:bg-coolgray-200 dark:hover:bg-coolgray-300"
                        >
                            Cancel
                        </x-forms.button>
                    </template>
                    
                    <template x-if="step === 1">
                        <x-forms.button 
                            @click="step++" 
                            x-bind:disabled="selectedActions.length === 0"
                            class="w-auto" 
                            isError
                        >
                            <span x-text="step1ButtonText"></span>
                        </x-forms.button>
                    </template>
                    
                    <template x-if="step === 2">
                        <x-forms.button 
                            @click="step === finalStep ? executeAction() : step++" 
                            x-bind:disabled="confirmWithText && userConfirmText !== confirmText"
                            class="w-auto" 
                            isError
                        >
                            <span x-text="step === finalStep ? step3ButtonText : step2ButtonText"></span>
                        </x-forms.button>
                    </template>
                    
                    <template x-if="step === 3">
                        <x-forms.button 
                            @click="executeAction()" 
                            x-bind:disabled="confirmWithPassword && !password"
                            class="w-auto" 
                            isError
                        >
                            <span x-text="step3ButtonText"></span>
                        </x-forms.button>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>


