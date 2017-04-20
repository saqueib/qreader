import { storage } from '../utils'

// State of app
const state = {
  channels: storage.get('qr_channels') || [],
  favs: storage.get('qr_favs') || [],
  showAddChannel: false,
  quotes: getQuotes(),
  collapseSideBar: storage.get('qr_sidebar_collapse') || false
}

// some quotes
function getQuotes() {
    return [
        "Happiness is an attitude. We either make ourselve…e amount of work is the same. -- Carlos Castaneda",
        "When one door of happiness closes, another opens,… one that has been opened for us. -- Helen Keller",
        "You have brains in your head. You have feet in yo… yourself, any direction you choose. -- Dr. Seuss",
        "Your work is going to fill a large part of your l… great work is to love what you do. -- Steve Jobs",
        "Instead of wondering when your next vacation is, …life you don’t need to escape from. -- Seth Godin",
        "The problem with the rat race is that even if you win, you’re still a rat. -- Lily Tomlin",
        "A man should never neglect his family for business. -- Walt Disney",
        "Don’t say you don’t have enough time. You have ex…son, and Albert Einstein. -- H. Jackson Brown Jr.",
        "Someone once told me that 'time' is a predator th…ause it will never come again. -- Jean-Luc Picard",
        "It is not the strongest of the species that survi… one most responsive to change. -- Charles Darwin",
        "Opportunity is missed by most people because it i…in overalls and looks like work. -- Thomas Edison",
        "You miss 100 percent of the shots you don’t take. -- Wayne Gretzky",
        "Do or do not. There is no try. -- Yoda",
        "Those who say it can not be done, should not interrupt those doing it. -- Chinese Proverb",
        "Whatever the mind of man can conceive and believe… let anyone else hold the pen. -- Harley Davidson",
        "A professional is someone who can do his best work when he doesn't feel like it. -- Alistair Cook",
        "Happiness is not something you postpone for the f…y drain you - pick them wisely. -- Hans F. Hansen",
        "The Pessimist complains about the wind. The optim…esponsibility for changing them. -- Denis Waitley"
    ];
}

export default state
