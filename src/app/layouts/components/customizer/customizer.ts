import { Component } from '@angular/core';
import { NgIcon } from '@ng-icons/core';
import { SimplebarAngularModule } from 'simplebar-angular';
import { LayoutService } from '../../../common/application/services/layout-store.service';

type Option = { id: string; value: string; label: string };

@Component({
  selector: 'app-customizer',
  imports: [NgIcon, SimplebarAngularModule],
  templateUrl: './customizer.html',
  styles: ``,
})
export class Customizer {

  sidenavOptions = [
    {
      id: 'sidenav-view-default',
      value: 'default',
      label: 'Default',
      imageui: ` <span class="block w-8 bg-default-100">   
       <span class="mt-1.5 mx-1.5 block space-y-1">
                                            <span class="h-1 block rounded-sm mb-2.5 bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                        </span>
                                        </span>`,
      imageui2: `
             <span class="flex items-center justify-end h-full mr-1.5">
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-hover', value: 'hover', label: 'Hover',
      imageui: `   <span class="w-3 block bg-default-100">
                                        <span class="w-1.5 h-1.5 mt-1 mx-auto rounded-sm bg-default-300"></span>
                                        <span class="flex flex-col items-center w-full mt-1.5 space-y-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                        </span>
                                    </span>`,
      imageui2: `
               <span class="flex items-center justify-end h-full mr-1.5">
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-hover-active',
      value: 'hover-active',
      label: 'Hover Active',
      imageui: `     <span class="w-8 bg-default-100">
                                        <span class="mt-1.5 mx-1.5 block space-y-1">
                                            <span class="flex mb-2.5 gap-1">
                                                <span class="h-1 block w-full rounded-sm bg-default-300"></span>
                                                <span class="h-1 block w-2 rounded-full bg-default-300"></span>
                                            </span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                            <span class="h-1 block rounded-sm bg-default-300"></span>
                                        </span>
                                    </span>`,
      imageui2: `
               <span class="flex items-center justify-end h-full mr-1.5">
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-sm', value: 'sm', label: 'Small',
      imageui: `        <span class="w-3 block bg-default-100">
                                        <span class="w-1.5 h-1.5 mt-1 mx-auto rounded-sm bg-default-300"></span>
                                        <span class="flex flex-col items-center w-full mt-1.5 space-y-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                            <span class="w-1.5 h-1.5 rounded-full bg-default-300"></span>
                                        </span>
                                    </span>`,
      imageui2: `
                  <span class="flex items-center h-full mr-1.5">
                                                <span class="grow">
                                                    <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                </span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-compact', value: 'md', label: 'Compact',
      imageui: `            <span class="w-4 block bg-default-100">
                                        <span class="w-2 h-2 mt-2 mx-auto rounded-sm bg-default-300"></span>
                                        <span class="flex flex-col items-center w-full mt-2 space-y-1">
                                            <span class="w-2 h-2 rounded-sm bg-default-300"></span>
                                            <span class="w-2 h-2 rounded-sm bg-default-300"></span>
                                            <span class="w-2 h-2 rounded-sm bg-default-300"></span>
                                        </span>
                                    </span>`,
      imageui2: `
            <span class="flex items-center h-full mr-1.5">
                                                <span class="grow">
                                                    <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                </span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ml-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-mobile', value: 'offcanvas', label: 'Mobile',
      imageui2: `
              <span class="flex items-center h-full mr-1.5">
                                                <span class="w-1.5 h-1.5  ms-1 rounded-sm bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1  rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-auto rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
    {
      id: 'sidenav-view-hidden', value: 'hidden', label: 'Hidden',
      imageui2: `
               <span class="flex flex-auto items-center h-full me-1.5">
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-auto rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                                <span class="w-1 h-1 block ms-1 rounded-full bg-default-300"></span>
                                            </span>
     `,
    },
  ];

  themeOptions: Option[] = [
    { id: 'layout-color-light', value: 'light', label: 'Light' },
    { id: 'layout-color-dark', value: 'dark', label: 'Dark' },
    { id: 'layout-color-system', value: 'system', label: 'System' },
  ];

  directionOptions: Option[] = [
    { id: 'direction-ltr', value: 'ltr', label: 'LTR Mode' },
    { id: 'direction-rtl', value: 'rtl', label: 'RTL Mode' },
  ];

  menuColorOptions: Option[] = [
    { id: 'menu-color-light', value: 'light', label: 'Light' },
    { id: 'menu-color-dark', value: 'dark', label: 'Dark' },
  ];

  constructor(public layout: LayoutService) { }

  isSidenavActive(value: string) {
    return this.layout.state().sidenav.size === value;
  }

  isThemeActive(value: string) {
    return this.layout.state().theme === value;
  }

  isMenuColorActive(value: string) {
    return this.layout.state().sidenav.color === value;
  }

  isDirectionActive(value: string) {
    return this.layout.state().direction === value;
  }

  setSidenav(value: string) {
    this.layout.setSidenavSize(value as any);
  }

  setTheme(value: string) {
    this.layout.setTheme(value as any);
  }

  setDirection(value: string) {
    this.layout.setDirection(value as any);
  }

  setMenuColor(value: string) {
    this.layout.setSidenavColor(value as any);
  }
  
  reset() {
    this.layout.reset();
  }

  toggleFullscreen() {
  const btn = document.getElementById('fullscreenBtn');

  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen().then(() => {
      btn?.classList.add('fullscreen-active');
    });
  } else {
    document.exitFullscreen().then(() => {
      btn?.classList.remove('fullscreen-active');
    });
  }
}

}
