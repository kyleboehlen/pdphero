<form method="POST"
    @isset($affirmation)
        action="{{ route('affirmations.update', ['affirmation' => $affirmation]) }}"
    @else
        action="{{ route('affirmations.store') }}"
    @endisset >

    @csrf

    <h2>@isset($affirmation)Edit @else Add @endisset Affirmation</h2><br/><br/>

    <textarea name="affirmation" rows="4" maxlength="255" placeholder="Your positive statement goes here!" required>@isset($affirmation){{ $affirmation->value }}@endisset</textarea>
    @error('affirmation')
        <p class="error">{{ $message }}</p>
    @enderror
        
    <a
        @isset($affirmation)
            href="{{ route('affirmations.show', ['affirmation' => $affirmation]) }}"
        @else
            href="{{ route('affirmations') }}"
        @endisset >
        <button class="cancel" type="button">Cancel</button>
    </a>

    <button class="submit" type="submit">Submit</button>
</form>