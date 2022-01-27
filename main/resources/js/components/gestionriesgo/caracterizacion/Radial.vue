<template>
    <div class="radial" id="chartdiv2"></div>
</template>

<script>
import * as am4core from "@amcharts/amcharts4/core";
import * as am4charts from "@amcharts/amcharts4/charts";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";

/* Chart code */
// Themes begin
am4core.useTheme(am4themes_animated);

export default {
    props: {
       datos: [Object, Array]
    },
    mounted(){
        am4core.useTheme(am4themes_animated);
        // Themes end

        /* Create chart instance */
        let chart = am4core.create("chartdiv2", am4charts.RadarChart);

        /* Add data */
        chart.data = [{
            "diagnostico":"HIPERTENSION ESENCIAL (PRIMARIA)",
            "mujeres":3804,
            "hombres":2097
        },
        {
            "diagnostico":"HIPERPLASIA DE LA PROSTATA",
            "mujeres":105,
            "hombres":2326
        },
        {
            "diagnostico":"CONTROL DE SALUD DE RUTINA DEL NINO",
            "mujeres":1182,
            "hombres":1213
        },
        {
            "diagnostico":"LUMBAGO NO ESPECIFICADO",
            "mujeres":837,
            "hombres":680
        },
        {
            "diagnostico":"DOLOR EN ARTICULACION",
            "mujeres":705,
            "hombres":333
        }
        /*,
        {
            "diagnostico":"INSUFICIENCIA VENOSA (CRONICA) (PERIFERICA)",
            "mujeres":674,
            "hombres":170
        },
        {
            "diagnostico":"INSUFICIENCIA RENAL CRONICA,NO ESPECIFICADA",
            "mujeres":430,
            "hombres":412
        },
        {
            "diagnostico":"HIPOTIROIDISMO,NO ESPECIFICADO",
            "mujeres":692,
            "hombres":146
        },
        {
            "diagnostico":"EXAMEN DE SEGUIMIENTO CONSECUTIVO A PSICOTERAPIA",
            "mujeres":659,
            "hombres":148
        }
        ,
        {
            "diagnostico":"CARIES DE LA DENTINA",
            "mujeres":428,
            "hombres":367
        }*/
        ];

        /* Create axes */
        let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "diagnostico";

        let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.renderer.axisFills.template.fill = chart.colors.getIndex(2);
        valueAxis.renderer.axisFills.template.fillOpacity = 0.05;

        valueAxis.extraMin = 0.2;
        valueAxis.extraMax = 0.2;
        valueAxis.tooltip.disabled = false;

        /* Create and configure series */
        let series = chart.series.push(new am4charts.RadarSeries());
        series.dataFields.valueY = "mujeres";
        series.dataFields.categoryX = "diagnostico";
        series.name = "Mujeres";
        series.strokeWidth = 3;
        series.tooltipText = "{valueY}";
        series.bullets.create(am4charts.CircleBullet);
        series.dataItems.template.locations.dateX = 0.5;

        let series2 = chart.series.push(new am4charts.RadarSeries());
        series2.dataFields.valueY = "hombres";
        series2.dataFields.categoryX = "diagnostico";
        series2.name = "Hombres";
        series2.strokeWidth = 3;
        series2.tooltipText = "{valueY}";
        series2.bullets.create(am4charts.CircleBullet);
        series2.dataItems.template.locations.dateX = 0.5;

        chart.cursor = new am4charts.RadarCursor();
        chart.legend = new am4charts.Legend();
    }
}
</script>

<style>
    .radial{
        height:400px;
    }
</style>