<script>
    window.deleteButtonTrans = '{{ trans("quickadmin.qa_delete_selected") }}';
    window.copyButtonTrans = '{{ trans("quickadmin.qa_copy") }}';
    window.csvButtonTrans = '{{ trans("quickadmin.qa_csv") }}';
    window.excelButtonTrans = '{{ trans("quickadmin.qa_excel") }}';
    window.pdfButtonTrans = '{{ trans("quickadmin.qa_pdf") }}';
    window.printButtonTrans = '{{ trans("quickadmin.qa_print") }}';
    window.colvisButtonTrans = '{{ trans("quickadmin.qa_colvis") }}';
</script>
<script src="{{ url('adminlte/js/jquery-1.11.3.min.js') }}"></script>
<script src="{{ url('adminlte/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('adminlte/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('adminlte/js/buttons.flash.min.js') }}"></script>
<script src="{{ url('adminlte/js/jszip.min.js') }}"></script>
<script src="{{ url('adminlte/js/pdfmake.min.js') }}"></script>
<script src="{{ url('adminlte/js/vfs_fonts.js') }}"></script>
<script src="{{ url('adminlte/js/buttons.html5.min.js') }}"></script>
<script src="{{ url('adminlte/js/buttons.print.min.js') }}"></script>
<script src="{{ url('adminlte/js/buttons.colVis.min.js') }}"></script>
<script src="{{ url('adminlte/js/dataTables.select.min.js') }}"></script>
<script src="{{ url('adminlte/js/jquery-ui.min.js') }}"></script>
<script src="{{ url('adminlte/js/bootstrap.min.js') }}"></script>
<script src="{{ url('adminlte/js/select2.full.min.js') }}"></script>
<script src="{{ url('adminlte/js/sweetalert.min.js') }}"></script>
<script src="{{ url('adminlte/js/main.js?v=2') }}"></script>

<script src="{{ url('adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ url('adminlte/plugins/fastclick/fastclick.js') }}"></script>
<script src="{{ url('adminlte/js/app.min.js') }}"></script>
<script>
    window._token = '{{ csrf_token() }}';
</script>
<script>
    $.extend(true, $.fn.dataTable.defaults, {
        "language": {
            "url": "{{ url('adminlte/js/Russian.json') }}"
        }
    });

     

</script>

<script>
    $(function(){
        /** add active class and stay opened when selected */
        var url = window.location;

        // for sidebar menu entirely but not cover treeview
        $('ul.sidebar-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        $('ul.treeview-menu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

        // for treeview
        $('ul.treeview-menu a').filter(function() {
             return this.href == url;
        }).parentsUntil('.sidebar-menu > .treeview-menu').addClass('menu-open').css('display', 'block');
    });
</script>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/air-datepicker/2.2.3/css/datepicker.min.css"
      integrity="sha256-q0wZgzQ6Ru67x+pIe9IAztrnlHNHyZui3BMwqUYDnpI=" crossorigin="anonymous"/>
<script defer src="{{ url('adminlte/js/datepicker.min.js') }}"></script>


@yield('javascript')
@yield('scripts')
