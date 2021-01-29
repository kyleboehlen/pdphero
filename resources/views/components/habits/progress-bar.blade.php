<div class="progress-container">
    <div class="progress-bar {{ $habit->strength == 0 ? 'no-border' : '' }}"
        style="background-color:{{ $habit->getRGB() }};padding-left: {{ $habit->getPadding() }}%;width:{{ $habit->strength == 0 ? 100 : $habit->strength }}%">
            <span @if($habit->isLowPercentage()) class="low-percentage" @endif>
                {{ $habit->strength }}%
            </span>
    </div>
</div>