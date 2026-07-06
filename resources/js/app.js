import './bootstrap';

import Alpine from 'alpinejs';
import { registerWilayahAlamat } from './wilayah-alamat';
import { registerLembagaLokasiWilayah } from './lembaga-lokasi-wilayah';
import { registerTugasSoalBuilder } from './tugas-soal-builder';
import './lembaga-wizard';

window.Alpine = Alpine;

registerWilayahAlamat();
registerLembagaLokasiWilayah();
registerTugasSoalBuilder();

Alpine.start();
