import type { IStaticMethods } from "preline/dist";

declare global {
  interface Window {

    HSStaticMethods: IStaticMethods;
  }
}

declare module 'granim';

export {};