<script>
    const isPublicVitrina = @json(route('public.vitrina')) === @json(request()->url());
    const goal = isPublicVitrina ? 'click_offer_public_vitrina' : 'click_offer';

    document.querySelectorAll('.offer_click').forEach((el) => {
        el.addEventListener('click', () => {
            ym(96714912, 'reachGoal', goal);
        });
    });
</script>
