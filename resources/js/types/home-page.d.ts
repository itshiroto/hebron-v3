/**
 * Represents the data structure for the Hero Section of the Home Page.
 */
export interface HeroSectionData {
    title: string;
    image?: string; // Path to the background image
    primary_button: {
        text: string;
        link: string;
    };
    secondary_button: {
        text: string;
        link: string;
    };
}

/**
 * Represents the data structure for the Vision and Mission Section of the Home Page.
 */
export interface VisionMissionSectionData {
    title: string;
    visi: {
        title: string;
        desc: string;
    };
    misi: {
        title: string;
        desc: string;
    };
    about_button_text: string;
}

/**
 * Represents a list of image paths for the Carousel Section.
 */
export type CarouselSectionData = string[];

/**
 * Represents a single service item within the Schedule Section.
 */
export interface ServiceItem {
    title: string;
    time: string;
}

/**
 * Represents the data structure for the Schedule Section of the Home Page.
 */
export interface ScheduleSectionData {
    title: string;
    subtitle: string;
    img?: string; // Path to the background image
    services: ServiceItem[];
    division_button_text: string;
}

/**
 * Represents the data structure for the Pastor Greeting Section of the Home Page.
 */
export interface PastorGreetingSectionData {
    subtitle: string;
    pastors_name: string;
    pastor_img?: string; // Path to the pastor's image
    pastor_signature?: string; // Path to the pastor's signature image
    desc: string;
    pastoral_button_text: string;
}

/**
 * Represents the full set of props passed to the Welcome Vue component for the Home Page.
 */
export interface HomePageProps {
    hero: HeroSectionData;
    visionMission: VisionMissionSectionData;
    carousel: CarouselSectionData;
    schedule: ScheduleSectionData;
    pastorGreeting: PastorGreetingSectionData;
}
