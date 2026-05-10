import './bootstrap';

import Alpine from 'alpinejs';
import { registerWilayahAlamat } from './wilayah-alamat';
import { registerLembagaLokasiWilayah } from './lembaga-lokasi-wilayah';
import './lembaga-wizard';

window.Alpine = Alpine;

registerWilayahAlamat();
registerLembagaLokasiWilayah();

Alpine.start();
