// $.ajax({
//     url: '/getMenu',
//     dataType: 'json',
//     success: function (result) {
//         console.log(result);

//         let els = "";
//         $.each(result.menu, function (i, data) {
//             els += `
//         <div class="card col-3 m-2 p-2 shadow-sm">
//             <div class="image-content">
//                 <div class="card-image">
//                     <div class="menu-img" style="background-image:url(${data.image}); background-size: cover;"></div>
//                 </div>
//             </div>

//             <div class="card-content">
//                 <h2 class="name">${data.title}</h2>
//                 <p class="description mb-2">${data.ingredients}</p>
//                 <button class="btnx  button-add btn-outline-success">Add</button>
//             </div>
//         </div>`
//         });
//         $('#view_menu').prepend(els);
//     }
// });

const params = new Proxy(new URLSearchParams(window.location.search), {
  get: (searchParams, prop) => searchParams.get(prop),
});
const per_page = params.per_page ?? 5
const page = params.page ?? 1

$(document).ready((e) => {
  // load data pertama kali
  tampilData(page, per_page)
})

function tampilData(page, per_page = 5) {
  $('#siswa').show()
  $('.data').remove()
  $.ajax({
    url: `/getSiswa?page=${page}&per_page=${per_page}`,
    accept: 'application/json',
    success: (res) => {
      let tr = "";
      currentPage = parseInt(res.page)
      $.each(res.data, (i, data) => {
        tr += `<tr class="data" data-target-id=${data.id_siswa}>`
        tr += `<td class="text-center">${data.id_siswa}</td>`
        tr += `<td>${data.nama_siswa}</td>`
        tr += `<td class="">${data.gender_siswa}</td>`
        tr += `<td class="text-right">${data.nilai_siswa}</td>`
        tr += `<td class="text-center"><button class="btn btn-danger" onclick="hapusData(${data.id_siswa})">Hapus</button></tr>`
      })
      nextPage = (currentPage < res.total_page) ? '' : 'disabled'
      prevPage = (currentPage == 1) ? 'disabled' : ''
      $('table').append(tr)
      $('table').append(`<tr class="data">
            <td colspan="4" class="text-start"><span>Page ke - ${res.page} dari ${res.total_page} page dengan Jumlah Products yang tersedia ${res.jumlah}<span></td>
            <td class="text-center">
              <nav aria-label="Page navigation example">
                <ul class="pagination align-items-center">
                  <li class="page-item"><button class="page-link ${prevPage}" onclick="tampilData(${currentPage - 1})"><span aria-hidden="true">&laquo;</span> Prev</button></li>
                  <li class="page-item"><button class="page-link ${nextPage}" onclick="tampilData(${currentPage + 1})">Next <span aria-hidden="true">&raquo;</span></button></li>
                </ul>
              </nav>
            </td>
          </tr>`)
      $('#siswa').hide()


    },
    error: (res) => {
      const { msg } = JSON.parse(res.responseText)
      $('#siswa').hide()
      $('table').append(`<td colspan="5" class="text-center p-2" id="siswa"><h3>${msg}</h3></td>`)
    }
  })
}

// $('#modalTrigger').modal('show')

//tambah siswa
$('#tambahSiswaForm').submit(e => {
  e.preventDefault();
  $('#dataLoader').addClass('d-none')
  $('#tambahSiswaForm #dataLoader').removeClass('d-none')
  var fd = new FormData();
  const nama = $('#tambahSiswaForm input#name').val()
  const gender = $('#tambahSiswaForm #gender').val()
  const nilai = $('#tambahSiswaForm input#nilai').val()
  fd.append('nama', nama)
  fd.append('gender', gender)
  fd.append('nilai', nilai)
  $.ajax({
    url: '/addSiswa',
    method: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    success: function (response) {
      $('.data').remove()
      loadData(page, per_page)
      $('#tambahSiswaForm .form-control').val('')
      $('#dataLoader').addClass('d-none')
      $('.modal').modal('hide')
      $('#success').modal('show')
      setInterval(() => {
        $('#success').modal('hide')
      }, 800)
    },
    fail: (e) => {
      console.log(e);
    },
  })
})

//hapus data
const hapusData = (id) => {
  $('#hapusData #dataLoader').addClass('d-none')
  $('#hapusData').modal('show')
  $('#btn_hapus').click(() => {
    $('#hapusData #dataLoader').removeClass('d-none')
    $.ajax({
      url: `/removeSiswa/${id}`,
      method: 'DELETE',
      success: (res) => {
        $('#hapusData #dataLoader').addClass('d-none')
        $(`tr[data-target-id="${id}"]`).remove()
        $('.modal').modal('hide')
        $('#success').modal('show')
        loadData(currentPage)
        setInterval(() => {
          $('#success').modal('hide')
        }, 1000)
      }
    })
  })
}


