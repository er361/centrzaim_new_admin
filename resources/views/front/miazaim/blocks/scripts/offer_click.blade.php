<script>
    const isPublicVitrina = @json(route('public.vitrina')) === @json(request()->url());
    const goal = isPublicVitrina ? 'click_offer_public_vitrina' : 'click_offer_vitrina';

    document.querySelectorAll('.offer_click').forEach((el) => {
        el.addEventListener('click', () => {
            ym(99015882, 'reachGoal', goal);
        });
    });
</script>
