(function ($) {
  "use strict";

  var dlabSparkLine = (function () {
    var lebarLayar = $(window).width();

    var lineChart1 = function (data) {
      if ($("#barChart_1").length > 0) {
        const ctx = document.getElementById("barChart_1").getContext("2d");
        ctx.canvas.height = lebarLayar < 800 ? 100 : 120;

        new Chart(ctx, {
          type: "line", // 🔥 diubah dari bar ke line
          data: {
            labels: [
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
            datasets: [
              {
                label: "Jumlah Penjualan Obat",
                data: data,
                borderColor: "rgba(0,123,255,1)",
                backgroundColor: "rgba(0,123,255,0.2)", // area bawah garis
                borderWidth: 2,
                fill: true, // isi area bawah garis
                tension: 0.4, // bikin garis smooth
                pointBackgroundColor: "rgba(0,123,255,1)",
                pointRadius: 4,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
            },
            scales: {
              y: {
                beginAtZero: true,
              },
            },
          },
        });
      }
    };

    return {
      load: function () {
        if (window.chartData) {
          lineChart1(window.chartData);
        } else {
          console.error("chartData tidak tersedia!");
        }
      },
      resize: function () {},
    };
  })();

  $(window).on("load", function () {
    // Hanya jalankan chart jika berada di halaman yang memiliki #barChart_1 dan chartData tersedia
    if ($("#barChart_1").length > 0 && window.chartData) {
      dlabSparkLine.load();
    }
  });

  $(window).on("resize", function () {
    setTimeout(function () {
      dlabSparkLine.resize();
    }, 1000);
  });
})(jQuery);
