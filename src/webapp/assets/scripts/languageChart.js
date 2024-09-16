'use strict'
{
  (async function () {
    const url1 = '/webapp/languages.json';
    const url2 = '/webapp/langhours.json';

    async function languageChart() {
      const response1 = await fetch(url1);
      const languages = await response1.json();
      const response2 = await fetch(url2);
      const langhours = await response2.json();

      let type = 'doughnut';
      const ctx = document.getElementById('languageChart').getContext('2d');

      const data = {
        // labels: ['HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'SQL', 'SHELL', '情報システム基礎知識(その他)'],
        labels: languages,
        datasets: [{
          // data: [30, 20, 10, 5, 5, 20, 20, 10],
          data: langhours,
          backgroundColor: ['LightSkyBlue', 'SkyBlue', 'CornflowerBlue', 'RoyalBlue', 'Blue', 'MediumBlue', 'SlateBlue', 'BlueViolet'],
          borderWidth: 0,
        }],

      };

      const options = {
        plugins: {
          title: {
            display: true,
            text: '学習言語',
            align: 'start',
            font: {
              size: 15,
            },
          },
          legend: {
            display: false,
          },
          datalabels: {
            font: {
              size: 13,
            },
            formatter: function (value) {
              return value.toString() + "%";
            },
          },
        },
        pieHole: 0.2,
        animation: false,
        maintainAspectRatio: false,
      };

      const myChart = new Chart(ctx, {
        type: type,
        data: data,
        options: options,
        plugins: [ChartDataLabels],
      });
      myChart;
    };
    await languageChart();
  })();
}