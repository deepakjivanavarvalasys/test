@forelse($resultCampaignHistories as $history)
<li>
    <i class="task-icon bg-c-green"></i>
    <h6>
            <span data-toggle="tooltip" data-placement="top" data-original-title="{{ $history->user->employee_code.' - '.$history->user->email }}">
                {{ $history->user->full_name }}
            </span>
        <span class="float-right text-muted">{{ date('d M, Y \a\t h:i A', strtotime($history->created_at)) }}</span>
    </h6>
    <p class="text-muted">{!! $history->message !!}</p>
</li>
@empty
@endforelse
