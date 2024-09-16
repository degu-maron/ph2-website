'use strict'
{
  (async function () {
    const url1 = '/webapp/contents.json';
    const url2 = '/webapp/conhours.json';

    async function contentChart() {
      const response1 = await fetch(url1);
      const contents = await response1.json();
      const response2 = await fetch(url2);
      const conhours = await response2.json();

      let type = 'doughnut';
      const ctx = document.getElementById('studyChart').getContext('2d');

      const data = {
        // labels: ["N予備校", "ドットインストール", "課題"],
        labels: contents,
        datasets: [{
          // data: [40, 20, 40],
          data: conhours,
          backgroundColor: ['LightSkyBlue', 'CornflowerBlue', 'RoyalBlue'],
          borderWidth: 0,
        }],
      };
      
      const options = {
        plugins: {
          title: {
            display: true,
            text: '学習コンテンツ',
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
              let data_sum = 0;
              data.datasets.foreach((dataset)=>{
                data_sum = data_sum + dataset,
              })
              return value.toString() + "%";
            },
          },
        },
        pieHole: 0.2,
        animation: false,
        maintainAspectRatio: false,
      };
 
      console.log(data.datasets[data]);

      const myChart = new Chart(ctx, {
        type: type,
        data: data,
        options: options,
        plugins: [ChartDataLabels],
      });

      myChart;
    };
    await contentChart();
  })();

}