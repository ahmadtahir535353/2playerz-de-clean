<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $fieldNames = $getFieldNames();
    @endphp
    @php
        $translations = [
            'title' => __('messages.other_lang.seo_analysis.title'),
            'analyzing' => __('messages.other_lang.seo_analysis.analyzing'),
            'problems' => __('messages.other_lang.seo_analysis.problems'),
            'improvements' => __('messages.other_lang.seo_analysis.improvements'),
            'good_practices' => __('messages.other_lang.seo_analysis.good_practices'),
            'empty_state' => __('messages.other_lang.seo_analysis.empty_state'),
            'title_missing' => __('messages.other_lang.seo_analysis.title_missing'),
            'title_too_short' => __('messages.other_lang.seo_analysis.title_too_short'),
            'title_too_long' => __('messages.other_lang.seo_analysis.title_too_long'),
            'title_length_good' => __('messages.other_lang.seo_analysis.title_length_good'),
            'keyword_not_in_title' => __('messages.other_lang.seo_analysis.keyword_not_in_title'),
            'keyword_in_title' => __('messages.other_lang.seo_analysis.keyword_in_title'),
            'description_missing' => __('messages.other_lang.seo_analysis.description_missing'),
            'description_too_short' => __('messages.other_lang.seo_analysis.description_too_short'),
            'description_too_long' => __('messages.other_lang.seo_analysis.description_too_long'),
            'description_length_good' => __('messages.other_lang.seo_analysis.description_length_good'),
            'keyword_not_in_description' => __('messages.other_lang.seo_analysis.keyword_not_in_description'),
            'keyword_in_description' => __('messages.other_lang.seo_analysis.keyword_in_description'),
            'content_too_short' => __('messages.other_lang.seo_analysis.content_too_short'),
            'content_length_good' => __('messages.other_lang.seo_analysis.content_length_good'),
            'content_length_excellent' => __('messages.other_lang.seo_analysis.content_length_excellent'),
            'keyword_not_in_content' => __('messages.other_lang.seo_analysis.keyword_not_in_content'),
            'keyword_appears_once' => __('messages.other_lang.seo_analysis.keyword_appears_once'),
            'keyword_stuffing' => __('messages.other_lang.seo_analysis.keyword_stuffing'),
            'keyword_density_good' => __('messages.other_lang.seo_analysis.keyword_density_good'),
            'add_focus_keyword' => __('messages.other_lang.seo_analysis.add_focus_keyword'),
        ];
    @endphp
    <div 
        x-data="seoBox({
            titleField: '{{ $fieldNames['title'] }}',
            contentField: '{{ $fieldNames['content'] }}',
            seoTitleField: '{{ $fieldNames['seo_title'] }}',
            seoDescriptionField: '{{ $fieldNames['seo_description'] }}',
            focusKeywordField: '{{ $fieldNames['focus_keyword'] }}',
            statePath: '{{ $getStatePath() }}',
            translations: @js($translations)
        })"
        class="fi-fo-field-wrp"
    >
        <div class="seo-box bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <!-- SEO Score Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.other_lang.seo_analysis.title') }}</h3>
                <div class="flex items-center gap-2">
                    <div 
                        x-show="seoScore !== null"
                        class="flex items-center gap-2"
                    >
                        <div 
                            :class="{
                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': seoScore < 50,
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': seoScore >= 50 && seoScore < 75,
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': seoScore >= 75
                            }"
                            class="px-3 py-1 rounded-full text-sm font-semibold"
                        >
                            <span x-text="seoScore + '%'"></span>
                        </div>
                    </div>
                    <div 
                        x-show="seoScore === null"
                        class="text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ __('messages.other_lang.seo_analysis.analyzing') }}
                    </div>
                </div>
            </div>

            <!-- Problems Section -->
            <div x-show="problems.length > 0" class="mb-4">
                <h4 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('messages.other_lang.seo_analysis.problems') }}
                </h4>
                <ul class="space-y-1">
                    <template x-for="(problem, index) in problems" :key="`problem-${index}`">
                        <li class="text-sm text-red-600 dark:text-red-400 flex items-start gap-2">
                            <span class="mt-0.5">•</span>
                            <span x-text="String(problem)"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Improvements Section -->
            <div x-show="improvements.length > 0" class="mb-4">
                <h4 class="text-sm font-semibold text-yellow-600 dark:text-yellow-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('messages.other_lang.seo_analysis.improvements') }}
                </h4>
                <ul class="space-y-1">
                    <template x-for="(improvement, index) in improvements" :key="`improvement-${index}`">
                        <li class="text-sm text-yellow-600 dark:text-yellow-400 flex items-start gap-2">
                            <span class="mt-0.5">•</span>
                            <span x-text="String(improvement)"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Good Practices Section -->
            <div x-show="goodPractices.length > 0" class="mb-4">
                <h4 class="text-sm font-semibold text-green-600 dark:text-green-400 mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('messages.other_lang.seo_analysis.good_practices') }}
                </h4>
                <ul class="space-y-1">
                    <template x-for="(practice, index) in goodPractices" :key="`practice-${index}`">
                        <li class="text-sm text-green-600 dark:text-green-400 flex items-start gap-2">
                            <span class="mt-0.5">•</span>
                            <span x-text="String(practice)"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <!-- Empty State -->
            <div x-show="seoScore === null && problems.length === 0 && improvements.length === 0 && goodPractices.length === 0" class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">
                {{ __('messages.other_lang.seo_analysis.empty_state') }}
            </div>
        </div>
    </div>
</x-dynamic-component>

