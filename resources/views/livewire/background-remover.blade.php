<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 sm:mb-8">
        <div>
            <flux:heading size="xl" level="1">Remove Background</flux:heading>
            <p class="text-slate-600 dark:text-slate-300 mt-1">Upload an image and remove its background instantly with precision</p>
        </div>
    </div>

    {{-- Upload Section --}}
    @if (!$processedImageUrl && !$isProcessing)
        <div class="flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-8">
                    <form wire:submit="removeBackground" class="space-y-4 sm:space-y-6">
                        {{-- File Upload Area --}}
                        <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-4 sm:p-8 text-center hover:border-indigo-400 transition-colors">
                            @if ($image)
                                <div class="space-y-4">
                                    @if ($image->isPreviewable())
                                        <div class="flex items-center justify-center">
                                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="max-h-32 sm:max-h-48 rounded-lg shadow-sm">
                                        </div>
                                    @else
                                        <div class="flex items-center justify-center">
                                            <div class="bg-slate-100 dark:bg-slate-600 rounded-lg p-8 text-center">
                                                <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">File selected</p>
                                            </div>
                                        </div>
                                    @endif
                                    <p class="text-sm text-slate-600 dark:text-slate-300">{{ $image->getClientOriginalName() }}</p>
                                    <flux:button type="button" wire:click="resetUpload" variant="ghost" size="sm">
                                        Choose different image
                                    </flux:button>
                                </div>
                            @else
                                <div class="space-y-4">
                                    <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div>
                                        <label for="file-upload" class="cursor-pointer">
                                            <span class="mt-2 block text-sm font-medium text-slate-900 dark:text-white">
                                                Click to upload an image
                                            </span>
                                            <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">
                                                PNG, JPG, GIF up to 10MB
                                            </span>
                                        </label>
                                        <input id="file-upload" wire:model="image" type="file" accept="image/*" class="sr-only">
                                    </div>
                                </div>
                            @endif
                        </div>

                        @error('image')
                            <div class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</div>
                        @enderror

                        @if ($image)
                            <div class="flex justify-center">
                                <flux:button type="submit" class="w-full sm:w-auto">
                                    Remove Background
                                </flux:button>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Processing Section --}}
    @if ($isProcessing)
        <div class="flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-8">
                    <div class="text-center space-y-4 sm:space-y-6">
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Processing your image...</h3>
                            <p class="text-slate-600 dark:text-slate-300">Advanced algorithms are working to remove the background</p>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full transition-all duration-500 ease-out" style="width: {{ $processingProgress }}%"></div>
                        </div>
                        <p class="text-sm text-slate-600 dark:text-slate-300">{{ $processingProgress }}% complete</p>

                        {{-- Animated Icon --}}
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Error Section --}}
    @if ($error)
        <div class="flex justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Processing failed</h3>
                            <p class="text-sm text-red-700 dark:text-red-300 mt-1">{{ $error }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <flux:button wire:click="resetUpload" variant="outline" size="sm">
                            Try again
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Results Section --}}
    @if ($processedImageUrl && !$isProcessing)
        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <div class="bg-white dark:bg-slate-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-8">
                    <div class="text-center space-y-4 sm:space-y-6">
                        <div class="space-y-2">
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Background removed successfully!</h3>
                            <p class="text-slate-600 dark:text-slate-300">Your image is ready for download</p>
                        </div>

                        {{-- Before/After Comparison --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                            @if ($originalImageUrl)
                                <div class="space-y-2">
                                    <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300">Original</h4>
                                    <img src="{{ $originalImageUrl }}" alt="Original" class="w-full max-h-48 sm:max-h-64 object-contain rounded-lg border border-slate-200 dark:border-slate-700">
                                </div>
                            @endif
                            <div class="space-y-2">
                                <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300">Background Removed</h4>
                                <div class="relative">
                                    <img src="{{ $processedImageUrl }}" alt="Processed" class="w-full max-h-48 sm:max-h-64 object-contain rounded-lg border border-slate-200 dark:border-slate-700">
                                    {{-- Checkered background to show transparency --}}
                                    <div class="absolute inset-0 -z-10 opacity-30" style="background-image: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), linear-gradient(-45deg, #f3f4f6 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f3f4f6 75%), linear-gradient(-45deg, transparent 75%, #f3f4f6 75%);
                                        background-size: 20px 20px;
                                        background-position: 0 0, 0 10px, 10px -10px, -10px 0px;"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Download Button --}}
                        <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                            <a href="{{ $processedImageUrl }}" download="background-removed.png" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Image
                            </a>
                            <flux:button wire:click="resetUpload" variant="outline" class="w-full sm:w-auto">
                                Process Another Image
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
