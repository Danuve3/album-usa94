{{-- This file is used for menu items by any Backpack v6 theme --}}
<x-backpack::menu-item title="Usuarios" icon="la la-users" :link="backpack_url('user')" />
<x-backpack::menu-item title="Páginas" icon="la la-file-image" :link="backpack_url('page')" />
<x-backpack::menu-item title="Cromos" icon="la la-images" :link="backpack_url('sticker')" />
<x-backpack::menu-item title="Asignar Cromos" icon="la la-th-large" :link="backpack_url('sticker-assigner')" />
<x-backpack::menu-item title="Mapeo de Cromos" icon="la la-map-marked-alt" :link="backpack_url('sticker-mapper')" />
<x-backpack::menu-item title="Códigos de canjeo" icon="la la-ticket-alt" :link="backpack_url('redeem-code')" />
<x-backpack::menu-item title="Configuración" icon="la la-cog" :link="backpack_url('setting')" />