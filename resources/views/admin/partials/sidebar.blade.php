@inject('request', 'Illuminate\Http\Request')
<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <i class="fa fa-user-circle fa-3x" style="color: #fff;"></i>
            </div>
            <div class="pull-left info">
                <p>{{\Illuminate\Support\Facades\Auth::user()->name}}</p>
                <span><i class="fa fa-circle text-success"></i>
                    {{isset(\Illuminate\Support\Facades\Auth::user()->role) ? \Illuminate\Support\Facades\Auth::user()->role->title : 'Роль не задана'}}
                </span>
            </div>
        </div>


        <ul class="sidebar-menu">
            @can('user_list')
                <li>
                    <a href="{{ route('admin.users.index') }}">
                        <i class="fa fa-user"></i>
                        <span>@lang('quickadmin.users.title')</span>
                    </a>
                </li>
            @endcan
            @can('user_search')
                <li>
                    <a href="{{ route('admin.users.search') }}">
                        <i class="fa fa-search"></i>
                        <span>Поиск по пользователям</span>
                    </a>
                </li>
            @endcan
            @can('webmaster_access')
                <li>
                    <a href="{{ route('admin.webmasters.index') }}">
                        <i class="fa fa-user-secret"></i>
                        <span>Вебмастера</span>
                    </a>
                </li>
            @endcan

            @can('loan_access')
                <li>
                    <a href="{{ route('admin.loans.index') }}">
                        <i class="fa fa-money"></i>
                        <span>Офферы для витрин</span>
                    </a>
                </li>
            @endcan

            @can('showcase_access')
                <li>
                    <a href="{{ route('admin.showcases.index') }}">
                        <i class="fa fa-laptop"></i>
                        <span>Витрины займов</span>
                    </a>
                </li>
            @endcan

            @can('banner_access')
                <li>
                    <a href="{{ route('admin.banners.index') }}">
                        <i class="fa fa-picture-o"></i>
                        <span>Баннеры</span>
                    </a>
                </li>
            @endcan

            @canany(['sms_access', 'sms_provider_access'])
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-mobile"></i> <span>SMS</span>
                        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('sms_access')
                            <li>
                                <a href="{{ route('admin.sms.index') }}">
                                    <span>Тексты сообщений</span>
                                </a>
                            </li>
                        @endcan

                        @can('sms_provider_access')
                            <li>
                                <a href="{{ route('admin.sms-providers.index') }}">
                                    <span>Аккаунты для отправки</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['revenue_report_access', 'diff_report_access', 'banner_report_access'])
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-line-chart"></i> <span>Отчеты</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('revenue_report_access')
                            <li>
                                <a href="{{ route('admin.report.revenue') }}">
                                    <i class="fa fa-calendar"></i>
                                    <span>Отчет по выручке</span>
                                </a>
                            </li>
                        @endcan
                        @can('diff_report_access')
                            <li>
                                <a href="{{ route('admin.report.diff') }}">
                                    <i class="fa fa-line-chart"></i>
                                    <span>Отчет по изменениям</span>
                                </a>
                            </li>
                        @endcan
                        @can('banner_report_access')
                            <li>
                                <a href="{{ route('admin.report.banner') }}">
                                    <i class="fa fa-picture-o"></i>
                                    <span>Отчет по баннерам</span>
                                </a>
                            </li>
                        @endcan
                        @can('sms_report_access')
                            <li>
                                <a href="{{ route('admin.report.sms') }}">
                                    <i class="fa fa-mobile"></i>
                                    <span>Отчет по SMS</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('source_access')
                <li>
                    <a href="{{ route('admin.sources.index') }}">
                        <i class="fa fa-building-o"></i>
                        <span>Партнерские программы</span>
                    </a>
                </li>
            @endcan

            @can('payment_access')
                <li>
                    <a href="{{ route('admin.payments.index') }}">
                        <i class="fa fa-money"></i>
                        <span>@lang('quickadmin.payments.title')</span>
                    </a>
                </li>
            @endcan

            @can('conversion_access')
                <li>
                    <a href="{{ route('admin.conversions.index') }}">
                        <i class="fa fa-arrow-circle-o-down" aria-hidden="true"></i>
                        <span>Конверсии</span>
                    </a>
                </li>
            @endcan

            @can('lead_service_access')
                <li>
                    <a href="{{ route('admin.lead-services.index') }}">
                        <i class="fa fa-book"></i>
                        <span>Отправка анкет</span>
                    </a>
                </li>
            @endcan

            @canany(['postback_access'])
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-cloud-upload"></i> <span>Экспорт</span>
                        <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('postback_access')
                            <li>
                                <a href="{{ route('admin.postbacks.index') }}">
                                    <span>Постбэки</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['postback_access'])
                <li class="treeview">
                    <a href="#">
                        <i class="fa fa-wrench"></i> <span>Инструменты</span>
                        <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                    </a>
                    <ul class="treeview-menu">
                        @can('postback_access')
                            <li>
                                <a href="{{ route('admin.postbacks.test.index') }}">
                                    <span>Тестирование постбэка</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('setting_access')
                <li>
                    <a href="{{ route('admin.settings.index') }}">
                        <i class="fa fa-gear"></i>
                        <span>@lang('quickadmin.settings.title')</span>
                    </a>
                </li>
            @endcan


            <li>
                <a href="#logout" onclick="$('#logout').submit();">
                    <i class="fa fa-arrow-left"></i>
                    <span class="title">@lang('quickadmin.qa_logout')</span>
                </a>
            </li>
        </ul>
    </section>
</aside>

