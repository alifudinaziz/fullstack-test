<template>
  <div class="bg-gray-200 flex justify-center">
    <div class="container p-8 lg:px-16 lg:py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="wrapper-total-pajak bg-white p-2 md:p-4 rounded-md shadow-md">
          <p class="font-bold text-lg mb-4">Total Pajak Terbayar</p>
          <p class="font-bold text-2xl md:text-3xl">Rp.{{ rekapTerbayar }}</p>
        </div>
        <div class="wrapper-total-pajak bg-white p-2 md:p-4 rounded-md shadow-md">
          <p class="font-bold text-lg mb-4">Total Pajak Tunggakan</p>
          <p class="font-bold text-2xl md:text-3xl">Rp.{{ rekapTunggakan }}</p>
        </div>
      </div>
      <div v-show="!showDetail" class="wrapper-daftar-pajak bg-white p-2 md:p-4 rounded-md shadow-md">
        <p class="font-bold">Daftar Pajak Kendaraan</p>
        <hr class="border-gray-300 mb-4">
        <div>
          <form class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
            <div class="wrapper-filter">
              <p class="font-bold mb-1">Jenis Kendaraan</p>
              <select
                class="bg-gray-200 border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                <option value="all">Semua</option>
                <option value="mobil">Mobil</option>
                <option value="motor">Motor</option>
              </select>
            </div>
            <div class="wrapper-filter">
              <p class="font-bold mb-1">Status Pajak</p>
              <select
                class="bg-gray-200 border border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2">
                <option value="all">Semua</option>
                <option value="terbayar">Terbayar</option>
                <option value="belum_bayar">Belum Bayar</option>
              </select>
            </div>
          </form>
        </div>
        <table class="w-full mb-4">
          <tr class="bg-gray-200">
            <th class="p-4">#</th>
            <th class="p-4">Nomor Plat</th>
            <th class="p-4">Jenis Kendaraan</th>
            <th class="p-4">Tanggal Pajak</th>
            <th class="p-4">Status Pajak</th>
            <th class="p-4">&nbsp</th>
          </tr>
          <tr v-show="!isLoading" v-for="(data, index) in listKendaraan" :key="index">
            <td class="text-center p-4">
              <p>{{ index + 1 }}</p>
            </td>
            <td class="text-center p-4">
              <p>{{ data.plat_nomor }}</p>
            </td>
            <td class="text-center p-4 uppercase">
              <p>{{ data.jenis_kendaraan }}</p>
            </td>
            <td class="text-center p-4">
              <p>{{ data.tgl_pajak }}</p>
            </td>
            <td class="text-center p-4">
              <div v-if="data.tgl_pembayaran === null" class="bg-red-500 text-white p-2 rounded-full">
                <p>Belum Bayar</p>
              </div>
              <div v-else class="bg-green-500 text-white p-2 rounded-full">
                <p>Terbayar</p>
              </div>
            </td>
            <td class="text-center p-4">
              <button class="bg-blue-400 text-white py-2 px-3 rounded-lg cursor-pointer"
                @click="showDetailSection(data)">
                Detail
              </button>
            </td>
          </tr>
        </table>
        <div v-show="isLoading">
          <p class="text-center">Loading...</p>
          <hr class="border-gray-300 mb-4">
        </div>
        <p class="text-center text-gray-400">Total Data {{ listKendaraanLength }}</p>
      </div>
      <div v-show="showDetail" class="wrapper-detail-pajak bg-white p-2 md:p-4 rounded-md shadow-md">
        <p class="font-bold">Detail Pajak Kendaraan</p>
        <hr class="border-gray-300 mb-4">
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Nomor Plat</p>
          <p class="leading-none ">{{ dataDetail.nomorPlat }}</p>
        </div>
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Nama</p>
          <p class="leading-none ">{{ dataDetail.nama }}</p>
        </div>
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Alamat</p>
          <p class="leading-none ">{{ dataDetail.alamat }}</p>
        </div>
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Jenis Kendaraan</p>
          <p class="leading-none ">{{ dataDetail.jenisKendaraan }}</p>
        </div>
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Nominal Pajak</p>
          <p class="leading-none ">{{ dataDetail.nominalPajak }}</p>
        </div>
        <div class="flex mb-4">
          <p class="leading-none w-48 font-semibold">Tanggal Pajak</p>
          <p class="leading-none ">{{ dataDetail.tanggalPajak }}</p>
        </div>
        <div class="flex">
          <p class="leading-none w-48 font-semibold">Status Pembayaran</p>
          <div v-if="dataDetail.tanggalBayar === null" class="bg-red-500 text-white py-1 px-2 rounded-full text-center">
            <p class="leading-none">Belum Bayar</p>
          </div>
          <div v-else class="bg-green-500 text-white py-1 px-2 rounded-full text-center">
            <p class="leading-none">Terbayar</p>
          </div>
        </div>
        <button @click="hideDetailSection"
          class="bg-blue-400 text-white py-2 px-3 rounded-lg cursor-pointer mt-4">Kembali</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import axios from 'axios'

const isLoading = ref(true)
const listKendaraan = ref([]);
const listKendaraanLength = ref(0);
const showDetail = ref(false);
const itemToShow = 10;
const rekapTerbayar = ref(0)
const rekapTunggakan = ref(0)
const dataDetail = reactive({
  alamat: '',
  jenisKendaraan: '',
  nama: '',
  nominalPajak: '',
  nomorPlat: '',
  tanggalPajak: '',
  tanggalBayar: null,
})

const showDetailSection = (paramData) => {
  showDetail.value = !showDetail.value

  dataDetail.alamat = paramData.alamat
  dataDetail.jenisKendaraan = paramData.jenis_kendaraan
  dataDetail.nama = paramData.nama
  dataDetail.nominalPajak = paramData.nominal_pajak
  dataDetail.nomorPlat = paramData.plat_nomor
  dataDetail.tanggalPajak = paramData.tgl_pajak
  dataDetail.tanggalBayar = paramData.tgl_pembayaran
}
const hideDetailSection = () => {
  showDetail.value = false

  dataDetail.alamat = ''
  dataDetail.jenisKendaraan = ''
  dataDetail.nama = ''
  dataDetail.nominalPajak = ''
  dataDetail.nomorPlat = ''
  dataDetail.tanggalPajak = ''
  dataDetail.tanggalBayar = null
}
const fetchData = async () => {
  const response = await fetch('/api/pajak/kendaraan');
  listKendaraan.value = await response.json();
  isLoading.value = false;
  listKendaraanLength.value = listKendaraan.value.data.length;
  listKendaraan.value = listKendaraan.value.data;
  listKendaraan.value = listKendaraan.value.slice(0, itemToShow);
}

onMounted(() => {
  fetchData();
});


</script>
