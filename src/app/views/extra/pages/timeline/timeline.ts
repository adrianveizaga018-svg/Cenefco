import { Component } from '@angular/core';
import { PageTitle } from "../../../../common/components/page-title/page-title";
import { NgIcon } from "@ng-icons/core";
import { RouterLink } from '@angular/router';

type TimelineItem = {
  title: string;
  subtitle?: string;
  date?: string;
  classes?: string; 
}

type TimelineType = {
  type: string; 
  items: TimelineItem[];
}

@Component({
  selector: 'app-timeline',
  imports: [PageTitle, NgIcon, RouterLink ],
  templateUrl: './timeline.html',
  styles: ``
})

export class Timeline {

  timelineData: TimelineType[] = [
  {
    type: 'Circle Timeline',
    items: [
      { 
        title: 'Invitation Sent', 
        subtitle: 'No candidate reply', 
        date: '02 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Sent to reminder to candidate', 
        subtitle: 'Preview message sent', 
        date: '15 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Scheduled', 
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Interview Done', 
        classes: 'relative px-6 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5'
      }
    ]
  },
  {
    type: 'Square Timeline',
    items: [
      { 
        title: 'Invitation Sent', 
        subtitle: 'No candidate reply', 
        date: '02 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Sent to reminder to candidate', 
        subtitle: 'Preview message sent', 
        date: '15 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Scheduled', 
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Interview Done', 
        classes: 'relative px-6 after:absolute after:size-2 after:bg-primary after:start-0 after:end-0 after:top-1.5'
      }
    ]
  },
  {
    type: 'Progress Timeline',
    items: [
      { 
        title: 'Invitation Sent', 
        subtitle: 'No candidate reply', 
        date: '02 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:end-0.75 before:border-primary before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Sent to reminder to candidate', 
        subtitle: 'Preview message sent', 
        date: '15 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:end-0.75 before:border-primary before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Scheduled', 
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-primary after:rounded-full after:start-0 after:end-0 after:top-1.5 pb-4'
      },
      { 
        title: 'Interview Done', 
        classes: 'relative px-6 after:absolute after:size-2 after:bg-card after:border after:border-default-200 after:rounded-full after:start-0 after:end-0 after:top-1.5'
      }
    ]
  },
  {
    type: 'Outline Timeline',
    items: [
      { 
        title: 'Invitation Sent', 
        subtitle: 'No candidate reply', 
        date: '02 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-card after:border after:rounded-full after:start-0 after:end-0 after:top-1.5 after:border-default-200 pb-4'
      },
      { 
        title: 'Sent to reminder to candidate', 
        subtitle: 'Preview message sent', 
        date: '15 Aug, 2023',
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-card after:border after:rounded-full after:start-0 after:end-0 after:top-1.5 after:border-default-200 pb-4'
      },
      { 
        title: 'Scheduled', 
        classes: 'relative px-6 before:absolute before:border-s before:start-0.75 before:border-default-200 before:end-0.75 before:top-1.5 before:-bottom-1.5 after:absolute after:size-2 after:bg-card after:border after:rounded-full after:start-0 after:end-0 after:top-1.5 after:border-default-200 pb-4'
      },
      { 
        title: 'Interview Done', 
        classes: 'relative px-6 after:absolute after:size-2 after:bg-card after:border-default-200 after:border after:rounded-full after:start-0 after:end-0 after:top-1.5'
      }
    ]
  }
];

}
