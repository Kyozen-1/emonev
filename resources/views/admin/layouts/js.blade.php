<!-- Vendor Scripts Start -->
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/jquery-3.5.1.min.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/OverlayScrollbars.min.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/autoComplete.min.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/clamp.min.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/icon/acorn-icons.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/icon/acorn-icons-interface.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/icon/acorn-icons-learning.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/glide.min.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/Chart.bundle.min.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/vendor/jquery.barrating.min.js') }}"></script>

<!-- Vendor Scripts End -->

<!-- Template Base Scripts Start -->
<script src="{{ asset('acorn/acorn-elearning-portal/js/base/helpers.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/base/globals.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/base/nav.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/base/search.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/base/settings.js') }}"></script>
<!-- Template Base Scripts End -->
<!-- Page Specific Scripts Start -->

<script src="{{ asset('acorn/acorn-elearning-portal/js/cs/glide.custom.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/cs/charts.extend.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/pages/dashboard.elearning.js') }}"></script>

<script src="{{ asset('acorn/acorn-elearning-portal/js/common.js') }}"></script>
<script src="{{ asset('acorn/acorn-elearning-portal/js/scripts.js') }}"></script>
<!-- Page Specific Scripts End -->
<script>
    const settings = new Settings({
        attributes: {
            color: "{{Auth::guard('admin')->user()->color_layout?Auth::guard('admin')->user()->color_layout:'light-blue'}}",
            navcolor: "{{Auth::guard('admin')->user()->nav_color?Auth::guard('admin')->user()->nav_color:'light'}}",
            behaviour: "{{Auth::guard('admin')->user()->behaviour?Auth::guard('admin')->user()->behaviour:'pinned'}}",
            layout: "{{Auth::guard('admin')->user()->layout?Auth::guard('admin')->user()->layout:'fluid'}}",
            radius: "{{Auth::guard('admin')->user()->radius?Auth::guard('admin')->user()->radius:'flat'}}",
            placement: "{{Auth::guard('admin')->user()->placement?Auth::guard('admin')->user()->placement:'vertical'}}",
        }
    });
    $('.option').click(function(){
        // var color_layout =  $('a[data-parent="color"]').attr('data-value');
        // var nav_color = $('a[data-parent="navcolor"]').attr('data-value');
        // var behaviour = $('a[data-parent="behaviour"]').attr('data-value');
        // var radius = $('a[data-parent="radius"]').attr('data-value');
        var color_layout =  $('#color a.option.active').attr('data-value');
        var nav_color = $('#navcolor a.option.active').attr('data-value');
        var behaviour = $('#behaviour a.option.active').attr('data-value');
        var layout = $('#layout a.option.active').attr('data-value');
        var radius = $('#radius a.option.active').attr('data-value');
        var placement = $('#placement a.option.active').attr('data-value');
        $.ajax({
            url: "{{ route('admin.dashboard.change') }}",
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                color_layout: color_layout,
                nav_color: nav_color,
                layout: layout,
                behaviour: behaviour,
                radius:radius,
                placement:placement
            }
        });
    });
</script>
@yield('js')
