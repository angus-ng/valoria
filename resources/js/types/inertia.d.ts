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
  role: 'user' | 'admin';
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
  flash?: {
    success?: string;
    error?: string;
    info?: string;
  }
}

declare module '@vue/runtime-core' {
  interface ComponentCustomProperties {
    $page: Page<AppPageProps>;
  }
}