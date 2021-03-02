<div class="progress-container">
    <div class="progress-bar {{ $percent == 0 ? 'no-border' : '' }}"
        style="background-color:{{ getRGB($percent) }};padding-left: {{ getPadding($percent) }}%;width:{{ $percent == 0 ? 100 : $percent }}%">
            <span @if(isLowPercentage($percent)) class="low-percentage" @endif>
                {{ $percent }}%
            </span>
    </div>
</div>