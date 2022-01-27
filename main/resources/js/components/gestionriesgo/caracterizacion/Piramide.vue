<template>
    <div class="piramide" id="chartdiv" v-if="datosuno.length"></div>
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import * as am4maps from "@amcharts/amcharts4/maps";

import am4themes_animated from "@amcharts/amcharts4/themes/animated";

am4core.useTheme(am4themes_animated);

export default {
    props: {
        datosuno: [Object, Array]
    },
    data() {
        return {
        }
    },
    mounted() {
        setTimeout(() => {
            this.CrearGrafica()
        }, 1500)
    },
    beforeDestroy() {
        if (this.chart) {
        this.chart.dispose();
        }
    },
    methods: {
        CrearGrafica(){
            let mainContainer = am4core.create("chartdiv", am4core.Container);
            mainContainer.width = am4core.percent(100);
            mainContainer.height = am4core.percent(100);
            mainContainer.layout = "horizontal";

            let usData = this.datosuno;

            usData.forEach(function(valor, indice, array) {
                usData[indice].age=valor.age.substring(2)
            })

            let maleChart = mainContainer.createChild(am4charts.XYChart);
            maleChart.paddingRight = 0;
            maleChart.data = JSON.parse(JSON.stringify(usData));

            // Create axes
            let maleCategoryAxis = maleChart.yAxes.push(new am4charts.CategoryAxis());
            maleCategoryAxis.dataFields.category = "age";
            maleCategoryAxis.renderer.grid.template.location = 0;
            //maleCategoryAxis.renderer.inversed = true;
            maleCategoryAxis.renderer.minGridDistance = 15;

            let maleValueAxis = maleChart.xAxes.push(new am4charts.ValueAxis());
            maleValueAxis.renderer.inversed = true;
            maleValueAxis.min = 0;
            maleValueAxis.max = 10;
            maleValueAxis.strictMinMax = true;

            maleValueAxis.numberFormatter = new am4core.NumberFormatter();
            maleValueAxis.numberFormatter.numberFormat = "#.#'%'";

            // Create series
            let maleSeries = maleChart.series.push(new am4charts.ColumnSeries());
            maleSeries.dataFields.valueX = "male";
            maleSeries.dataFields.valueXShow = "percent";
            maleSeries.calculatePercent = true;
            maleSeries.dataFields.categoryY = "age";
            maleSeries.interpolationDuration = 1000;
            maleSeries.columns.template.tooltipText = "Masculino, edad {categoryY}: {valueX} ({valueX.percent.formatNumber('#.0')}%)";
            //maleSeries.sequencedInterpolation = true;


            let femaleChart = mainContainer.createChild(am4charts.XYChart);
            femaleChart.paddingLeft = 0;
            femaleChart.data = JSON.parse(JSON.stringify(usData));

            // Create axes
            let femaleCategoryAxis = femaleChart.yAxes.push(new am4charts.CategoryAxis());
            femaleCategoryAxis.renderer.opposite = true;
            femaleCategoryAxis.dataFields.category = "age";
            femaleCategoryAxis.renderer.grid.template.location = 0;
            femaleCategoryAxis.renderer.minGridDistance = 15;

            let femaleValueAxis = femaleChart.xAxes.push(new am4charts.ValueAxis());
            femaleValueAxis.min = 0;
            femaleValueAxis.max = 10;
            femaleValueAxis.strictMinMax = true;
            femaleValueAxis.numberFormatter = new am4core.NumberFormatter();
            femaleValueAxis.numberFormatter.numberFormat = "#.#'%'";
            femaleValueAxis.renderer.minLabelPosition = 0.01;

            // Create series
            let femaleSeries = femaleChart.series.push(new am4charts.ColumnSeries());
            femaleSeries.dataFields.valueX = "female";
            femaleSeries.dataFields.valueXShow = "percent";
            femaleSeries.calculatePercent = true;
            femaleSeries.fill = femaleChart.colors.getIndex(4);
            femaleSeries.stroke = femaleSeries.fill;
            //femaleSeries.sequencedInterpolation = true;
            femaleSeries.columns.template.tooltipText = "Femenino, edad {categoryY}: {valueX} ({valueX.percent.formatNumber('#.0')}%)";
            femaleSeries.dataFields.categoryY = "age";
            femaleSeries.interpolationDuration = 1000;

        }
    }
}
</script>

<style>
  .piramide{
    height:400px;
  }
</style>