{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Páginas" icon="la la-file-image" :link="backpack_url('page')" />
<x-backpack::menu-item title="Cromos" icon="la la-images" :link="backpack_url('sticker')" />
<x-backpack::menu-item title="Usuarios" icon="la la-users" :link="backpack_url('user')" />
<x-backpack::menu-item title="Configuración" icon="la la-cog" :link="backpack_url('setting')" />