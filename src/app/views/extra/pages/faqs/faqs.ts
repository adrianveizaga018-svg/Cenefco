import { Component } from '@angular/core';
import { PageTitle } from '../../../../common/components/page-title/page-title';
import { FaqBanner } from "./components/faq-banner/faq-banner";
import { PopularQuestion } from "./components/popular-question/popular-question";
import { ProductVideo } from "./components/product-video/product-video";

@Component({
  selector: 'app-faqs',
  imports: [PageTitle, FaqBanner, PopularQuestion, ProductVideo],
  templateUrl: './faqs.html',
  styles: ``
})

export class Faqs {

}
