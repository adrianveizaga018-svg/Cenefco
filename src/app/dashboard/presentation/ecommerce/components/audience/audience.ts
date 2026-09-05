import { Component } from '@angular/core';
import { Apexchart } from "../../../../../common/components/apexchart/apexchart";
import { ApexOptions } from 'ng-apexcharts';
import { NgIcon } from "@ng-icons/core";
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-audience',
  imports: [Apexchart, NgIcon, RouterLink],
  templateUrl: './audience.html',
  styles: ``
})
export class Audience {
  
  audienceChartOptions: () => ApexOptions = () => ({
      series: [{
                name: 'Male',
                data: [44, 55, 41, 67, 22, 43, 26]
            }, {
                name: 'Female',
                data: [13, 23, 20, 8, 13, 27, 41]
            }],
            chart: {
                type: 'bar',
                height: 330,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 6,
                    columnWidth: '44%',
                    dataLabels: {
                        total: {
                            enabled: true,
                            style: {
                                fontSize: '13px',
                                fontWeight: 600
                            }
                        }
                    }
                },
            },
            xaxis: {
                type: 'datetime',
                categories: ['01/01/2023 GMT', '01/02/2023 GMT', '01/03/2023 GMT', '01/04/2023 GMT', '01/05/2023 GMT', '01/06/2023 GMT', '01/07/2023 GMT'],
            },

            colors: ["#ff6900", "#2b7fff"],

            legend: {
                position: 'top',
                horizontalAlign: 'right',
            },
            fill: {
                opacity: 1
            }
  })
   
}
