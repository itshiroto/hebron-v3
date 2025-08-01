/**
 * Represents the data structure for the Hero Section of the About
Page.
 */
export interface HeroSectionData {
    title: string;
    subtitle: string;
    image?: string; // Path to the background image
}

/**
 * Represents the data structure for the Vision and Mission
Section of the About Page.
 */
export interface VisionMissionSectionData {
    visi: {
        title: string;
        desc: string;
    };
    misi: {
        title: string;
        desc: string;
    };
    image?: string; // Path to the image
}

/**
 * Represents a single value item within the Values Section.
 */
export interface ValueItem {
    icon: string; // e.g., Font Awesome class
    title: string;
    desc: string;
}

/**
 * Represents a single history event item within the History
Section.
 */
export interface HistoryItem {
    title: string;
    desc: string;
    date: string; // e.g., "2023"
    image?: string; // Path to the image
}

export interface PastorItem {
    name: string;
    position: string;
    image?: string;
}

/**
 * Represents the full set of props passed to the AboutUs Vue component.
 */
export interface AboutPageProps {
    hero: HeroSectionData;
    visionMission: VisionMissionSectionData;
    values: ValueItem[];
    history: HistoryItem[];
    pastors: PastorItem[];
}
