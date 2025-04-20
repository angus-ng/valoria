export interface Crown {
  id: number;
  crown_type: 'small' | 'large';
}

export interface Habitat {
  id: number;
  name: string;
  icon_url?: string;
}

export interface Monster {
  id: number;
  name: string;
  slug: string;
  icon_url?: string;
  habitats: Habitat[];
  crowns: Crown[];
}