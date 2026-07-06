import Alpine from 'alpinejs';

export function registerTugasSoalBuilder() {
    const labels = 'ABCDEF';

    const emptyPilihan = (isBenar = false) => ({ teks: '', is_benar: isBenar });

    const emptySoal = () => ({
        pertanyaan: '',
        jawaban_benar: 0,
        pilihan: [
            emptyPilihan(true),
            emptyPilihan(false),
            emptyPilihan(false),
            emptyPilihan(false),
        ],
    });

    Alpine.data('tugasSoalBuilder', (initialJenis = 'esai', initialSoal = []) => ({
        jenisSoal: initialJenis,
        soal: [],

        init() {
            if (Array.isArray(initialSoal) && initialSoal.length > 0) {
                this.soal = initialSoal.map((item) => ({
                    pertanyaan: item.pertanyaan ?? '',
                    jawaban_benar: Number(item.jawaban_benar ?? 0),
                    pilihan: (item.pilihan ?? []).map((p, i) => ({
                        teks: p.teks ?? '',
                        is_benar: Number(item.jawaban_benar ?? 0) === i,
                    })),
                }));
            }

            if (this.jenisSoal === 'pilihan_ganda' && this.soal.length === 0) {
                this.addSoal();
            }
        },

        setJenis(jenis) {
            this.jenisSoal = jenis;
            if (jenis === 'pilihan_ganda' && this.soal.length === 0) {
                this.addSoal();
            }
        },

        labelFor(index) {
            return labels[index] ?? String(index + 1);
        },

        addSoal() {
            this.soal.push(emptySoal());
        },

        removeSoal(index) {
            if (this.soal.length <= 1) {
                return;
            }
            this.soal.splice(index, 1);
        },

        addPilihan(soalIndex) {
            const item = this.soal[soalIndex];
            if (!item || item.pilihan.length >= 6) {
                return;
            }
            item.pilihan.push(emptyPilihan(false));
        },

        removePilihan(soalIndex, pilihanIndex) {
            const item = this.soal[soalIndex];
            if (!item || item.pilihan.length <= 2) {
                return;
            }
            item.pilihan.splice(pilihanIndex, 1);
            if (item.jawaban_benar >= item.pilihan.length) {
                item.jawaban_benar = 0;
            }
        },

        setBenar(soalIndex, pilihanIndex) {
            const item = this.soal[soalIndex];
            if (!item) {
                return;
            }
            item.jawaban_benar = pilihanIndex;
            item.pilihan.forEach((p, i) => {
                p.is_benar = i === pilihanIndex;
            });
        },
    }));
}
