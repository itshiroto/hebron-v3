/**
 * Represents the data structure for the Hero Section of the About
Page.
 */
export interface HeroSectionData {
    title: string;
    text1: string;
    text2: string;
    image?: string; // Path to the background image
}

/**
 * Represents the data structure for the Vision and Mission
Section of the About Page.
 */
export interface VisionMissionSectionData {
    title: string;
    visi: string;
    misi: string;
    carousel?: string; // Path to the image
}

/**
 * Represents a single value item within the Values Section.
 */
export interface MinistriesItem {
    title: string;
    hours: string;
    image?: string; // Path to the image
    desc: string;
}

/**
 * Represents the full set of props passed to the AboutUs Vue component.
 */
export interface HomePageProps {
    hero: HeroSectionData;
    visionMission: VisionMissionSectionData;
    ministries: ValueItem[];
}
