<?php

namespace App\Filament\Resources\PollsResource\Pages;

use App\Filament\Resources\PollsResource;
use App\Models\PollResult;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Http\Request;

class PollVoteResult extends Page
{
    protected static string $resource = PollsResource::class;

    protected static string $view = 'filament.resources.polls-resource.pages.poll-vote-result';


    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return __('messages.poll.poll_result');
    }

    public function mount(Request $request)
    {

    }

    protected function getOption(): array
    {
        return [
            'option1', 'option2', 'option3', 'option4', 'option5', 'option6', 'option7', 'option8', 'option9', 'option10',
        ];
    }

    protected function getViewData(): array
    {
        $pollId = request()->route('record');

        $pollResults = PollResult::with('poll')->wherePollId($pollId)->get();
        $resultsAns = $pollResults->pluck('answer')->toArray();
        $totalPollResults = count($pollResults);
        $totalPerAns = array_count_values($resultsAns);
        $optionAns = [];
        foreach ($pollResults as $result) {
            $poll = $result->poll;
            foreach ($this->getOption() as $option) {
                if (! empty($poll->$option)) {
                    $optionAns[$poll->$option] = ! empty($totalPerAns[$poll->$option])
                        ? intval($totalPerAns[$poll->$option] * 100 / $totalPollResults) : 0;
                }
            }
        }

        $data['totalPollResults'] = $totalPollResults;
        $data['optionAns'] = $optionAns;
        $data['pollId'] = $pollId;

        // dd($data);
        return $data;
    }
}
