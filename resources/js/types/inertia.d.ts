import { Page } from '@inertiajs/inertia'
import { ComponentCustomProperties } from 'vue'

export interface LayoutPageProps extends Record<string, unknown> {
  name: string;
  quote?: {
    message: string;
    author: string;
  };
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface AppPageProps extends Record<string, unknown> {
  auth: {
    user: User;
  };
  ziggy?: {
    url: string;
    location: string;
    defaults: Record<string, any>;
    routes: Record<string, any>;
    [key: string]: unknown;
  };
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $page: Page<AppPageProps>;
  }
}