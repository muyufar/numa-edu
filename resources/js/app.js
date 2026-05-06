import './bootstrap';

import Alpine from 'alpinejs';
import { registerWilayahAlamat } from './wilayah-alamat';

window.Alpine = Alpine;

registerWilayahAlamat();

Alpine.start();
