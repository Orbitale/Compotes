import '../css/app.css';
import * as Highcharts from "highcharts";

// import $ from 'jquery';

console.info('App startup');

(async function () {

    const chartsContainer = document.getElementById('charts');

    if (chartsContainer) {
        try {
            const filtersUrl = chartsContainer.getAttribute('data-filters-url');

            const url = new URL(filtersUrl);
            url.searchParams.append('filters', '...');

            const result = await fetch(url);

            if (result.status !== 200) {
                throw new Error('Filters request returned an error.');
            }

            const json = await result.json();

            console.info('Filters fetch result: ', json);

            if (!json.highcharts) {
                throw new Error('Highcharts field is not defined in response.');
            }

            const myChart = Highcharts.chart('charts', json.highcharts);

            console.info('Chart: ', myChart);
        } catch (e) {
            console.error('Cannot fetch filters: '+e.message);
        }

    }

})();
