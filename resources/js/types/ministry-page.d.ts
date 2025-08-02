export interface HeroSectionData {
    title: string;
    image?: string; // Path to the background image
}

export interface IntroSectionData {
    title: string;
    desc: string;
}

export interface MinistryItem {
    title: string;
    desc: string;
    image?: string;
    link: string;
}

export interface ServeSectionData {
    title: string;
    ministryList: MinistryItem[];
}

export interface QuestionAnswer {
    question: string;
    answer: string;
}

export interface FAQSectionData {
    title: string;
    questionsList: QuestionAnswer[];
}

export interface MinistryPageSections {
    hero?: HeroSectionData;
    intro?: IntroSectionData;
    serve?: ServeSectionData;
    faq?: FAQSectionData;
}
