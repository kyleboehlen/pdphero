<form class="addiction" method="POST"
    @isset($addiction)
        action="{{ route('addiction.update', ['addiction' => $addiction->uuid]) }}"
    @else
        action="{{ route('addiction.store') }}"
    @endisset>

    @csrf

    {{-- Header --}}
    <h2>@isset($addiction) Edit Addiction @else Create New Addiction @endisset</h2>

    <input type="text" name="name" placeholder="Addiction Name" maxlength="255" 
        @isset($addiction)
            value="{{ $addiction->name }}"
        @else
            value="{{ old('name') }}" 
        @endisset required />

    @error('name')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    {{-- Start timestamp --}}
    <span class="start-on">
        Start On: <input id="start-on" type="date" name="start-date" value="{{ is_null($addiction) ? $carbon_now->format('Y-m-d') : $carbon_start->format('Y-m-d') }}" required 
            />&nbsp;@ @isset($addiction) {{ $carbon_start->format('h:i A') }} @else {{ $carbon_now->format('h:i A') }} @endisset
    </span>

    @error('start-date')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    <select id="method-selector" name="method" data-moderation="{{ $moderation }}" required>
        @foreach($methods as $method)
            <option value="{{ $method->id }}"
                @if(!is_null($addiction) && $addiction->method_id == $method->id)
                    selected
                @elseif(old('method') == $method->id)
                    selected
                @endif
            >{{ $method->name }}</option>
        @endforeach
    </select>

    @error('method')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/><br/>

    <span id="moderation-span" class="moderation {{ (!is_null($addiction) && $addiction->method_id == $moderation) ? '' : 'hide' }}">
        <input id="moderation-amount" name="moderation-amount" type="number" min="1"
            value="{{ is_null($addiction) ? (old('moderation-amount') ?? 1) : $addiction->moderated_amount ?? 1 }}">
        times per <span class="break"></span>
        <input id="moderation-period" name="moderation-period" type="number" min="1"
            value="{{ is_null($addiction) ? (old('moderation-period') ?? 1) : $addiction->moderated_period ?? 1 }}">
        <select name="moderation-date-format">
            @foreach($moderation_periods as $value => $period)
                <option value="{{ $value }}"
                    @if(!is_null($addiction) && $addiction->moderated_date_format_id == $value)
                        selected
                    @elseif(old('moderation-period') == $value)
                        selected
                    @endif
                >{{ $period['name'] }}</option>
            @endforeach
        </select>
    </span>

    @error('moderation-amount')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/>

    @error('moderation-period')
        <p class="error">{{ $message }}</p>
    @enderror
    <br/>

    <textarea name="details" placeholder="Why do you want to break this addiction, and what negative effects is it having on your life? What will you do instead when you feel tempted to cave, and what positives will you be rewarded with when you don't?"
        >@isset($addiction){{ $addiction->details }}@else{{ old('details') }}@endisset</textarea>

    @error('details')
        <p class="error">{{ $message }}</p>
    @enderror
    <span class="form-spacer"></span>

    <a href="{{ route('addictions') }}">
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>
    