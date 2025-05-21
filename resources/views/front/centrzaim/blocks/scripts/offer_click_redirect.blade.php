<script>
    // If redirect functionality is needed, uncomment and configure below
    
    // const redirectUrl = 'https://zaimi.credyshop.ru';
    
    document.addEventListener('click', function (event) {
        const link = event.target.closest('.offer_click');
        if (link && link.getAttribute('href') !== '#') {
            // Open the link in a new tab
            window.open(link.getAttribute('href'), '_blank');
            
            // For future use - if you need to redirect the current page
            // setTimeout(() => {
            //     window.location.href = redirectUrl;
            // }, 100);
            
            // Prevent default only if implementing redirect
            // event.preventDefault();
        }
    });
</script>