import { Component } from '@angular/core';
import { NgIcon } from "@ng-icons/core";

type FaqItem = {
  question: string;
  answer: string;
};

@Component({
  selector: 'app-popular-question',
  imports: [NgIcon],
  templateUrl: './popular-question.html',
  styles: ``
})

export class PopularQuestion {

  faqData: FaqItem[] = [
    {
      question: 'How to install Tailwind CSS in a project?',
      answer: `
      <p class="mb-3 text-sm">To install Tailwind CSS, you can use npm or yarn by running the following commands:</p>
      <ol class="flex flex-col gap-2 list-decimal list-inside">
        <li class="text-sm text-default-700">
          <b>Using npm</b>
          <div class="mt-2"><code>npm install tailwindcss</code></div>
        </li>
        <div class="text-center">OR</div>
        <li class="text-sm text-default-700">
          <b>Using yarn</b>
          <div class="mt-2"><code>yarn add tailwindcss</code></div>
        </li>
      </ol>
    `
    },
    
    {
      question: 'How to enable dark mode in Tailwind CSS?',
      answer: `
      <p class="mb-3 text-sm">To enable dark mode in Tailwind CSS, update your <code>tailwind.config.js</code> file with the <code>darkMode</code> option.</p>
      <p class="mb-3 text-sm">Using <code>class</code>, the dark mode is enabled by adding a <code>.dark</code> class to an ancestor element:</p>
      <pre ngNonBindable><code>
module.exports = {
  darkMode: 'class',
}
      </code></pre>
    `
    },
     {
      question: 'How to use plugins in Tailwind CSS?',
      answer: `
      <p class="mb-3 text-sm">To get started with your first plugin, import Tailwind’s <code>plugin</code> function from <code>tailwindcss/plugin</code>.</p>
      <pre ngNonBindable><code>
const plugin = require('tailwindcss/plugin')
module.exports = {
  plugins: [
    plugin(function({ addUtilities, addComponents, e, config }) {
    }),
  ]
}
      </code></pre>
    `
    }
   
  ];

   faqData2: FaqItem[] = [
     {
      question: 'How to customize the configuration file in Tailwind CSS?',
      answer: `
      <p class="mb-3 text-sm">You can customize the <code>tailwind.config.js</code> file:</p>
      <pre ngNonBindable><code>
module.exports = {
  purge: [],
  theme: {
    extend: {
      ...
    },
  },
  variants: {},
  plugins: [],
}
      </code></pre>
    `
    },
 {
      question: 'How to use responsive variants in Tailwind CSS?',
      answer: `
      <p class="mb-3 text-sm">Tailwind CSS generates responsive variants for most utilities:</p>
      <ul class="flex flex-col gap-2 list-disc list-inside mb-2 text-sm">
        <li><code>sm:</code>640px</li>
        <li><code>md:</code>768px</li>
        <li><code>lg:</code>1024px</li>
        <li><code>xl:</code>1280px</li>
      </ul>
      <pre ngNonBindable><code>Your content here</code></pre>
    `
    },
    
   ]

}
