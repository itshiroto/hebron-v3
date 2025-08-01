// Define the average reading speed in words per minute
const WORDS_PER_MINUTE = 200;

/**
 * Calculates the estimated reading time for a given HTML string.
 * @param {string} plainText The HTML content to estimate.
 * @returns {number} The formatted reading time string (e.g., "5 min read").
 */
export function getReadingTime(plainText: string): number {
    // Check if content exists
    if (!plainText) {
        return 0;
    }

    // Split the text into words by spaces and count them
    // The regex handles multiple spaces and newlines
    const wordCount = plainText.trim().split(/\s+/).length;

    // Calculate the reading time
    const minutes = Math.ceil(wordCount / WORDS_PER_MINUTE);

    // Format the output
    return minutes
}
