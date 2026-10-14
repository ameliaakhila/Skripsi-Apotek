(function ($) {
  "use strict";

  // HANYA initialize pickadate untuk input text dengan class datepicker-default
  // JANGAN initialize untuk input[type="date"] karena sudah pakai native HTML5 date picker
  $('input[type="text"].datepicker-default').pickadate({
    // Format untuk display
    format: "dd/mm/yyyy",
    // Format untuk value yang disimpan
    formatSubmit: "dd/mm/yyyy",
    // Auto close saat select date
    closeOnSelect: true,
    closeOnClear: true,
    // Container khusus
    container: "body",
    // Ensure proper month/year display
    selectYears: 100,
    selectMonths: true,
    // Min date (optional)
    min: new Date(1900, 0, 1),
    // Today button
    today: "Hari ini",
    clear: "Hapus",
    // Indonesian locale strings
    monthsFull: [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ],
    monthsShort: [
      "Jan",
      "Feb",
      "Mar",
      "Apr",
      "Mei",
      "Jun",
      "Jul",
      "Agu",
      "Sep",
      "Okt",
      "Nov",
      "Des",
    ],
    weekdaysFull: [
      "Minggu",
      "Senin",
      "Selasa",
      "Rabu",
      "Kamis",
      "Jumat",
      "Sabtu",
    ],
    weekdaysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
  });

  // Fix aria-hidden warning dengan event listener
  $(document).on("focus", 'input[type="text"].datepicker-default', function () {
    var pickerId = $(this).data("pick");
    if (pickerId) {
      $("#" + pickerId).removeAttr("aria-hidden");
    }
  });

  // Additional fix: Suppress console warning by patching picker behavior
  setTimeout(function () {
    var pickers = document.querySelectorAll(".picker");
    pickers.forEach(function (picker) {
      picker.removeAttribute("aria-hidden");
      picker.setAttribute("role", "dialog");
      picker.setAttribute("aria-label", "Date picker");
      picker.style.zIndex = "10100";
    });
  }, 500);
})(jQuery);
