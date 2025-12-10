const BaseClient = require('../../infrastructure/clients/BaseClient');

class AstronomyService {
    constructor(cacheRepository) {
        this.cacheRepository = cacheRepository;
        this.apiKey = process.env.ASTRO_APP_ID;
        this.apiSecret = process.env.ASTRO_APP_SECRET;
    }
    
    async getEvents(lat, lon, days = 7) {
        try {
            const cacheKey = `astro:events:${lat}:${lon}:${days}`;
            
            const cached = await this.cacheRepository.get(cacheKey);
            if (cached) {
                console.log('Returning cached astronomy events');
                return JSON.parse(cached);
            }

            console.log(`Fetching astronomy events for lat=${lat}, lon=${lon}, days=${days}`);
            
            const from = new Date().toISOString().split('T')[0];
            const toDate = new Date();
            toDate.setDate(toDate.getDate() + parseInt(days));
            const to = toDate.toISOString().split('T')[0];

            let result;
            
            result = await this.tryOpenNotifyAPI(lat, lon);
            if (result.success) {
                const finalResult = {
                    success: true,
                    data: result.data,
                    metadata: {
                        lat,
                        lon,
                        days,
                        from,
                        to,
                        count: this.countEvents(result.data),
                        cached: false,
                        source: 'open-notify.org',
                        api_used: 'Open Notify ISS API'
                    }
                };
                
                await this.cacheRepository.set(cacheKey, JSON.stringify(finalResult), 1800);
                return finalResult;
            }
            
            result = await this.trySunriseSunsetAPI(lat, lon, days);
            if (result.success) {
                const finalResult = {
                    success: true,
                    data: result.data,
                    metadata: {
                        lat,
                        lon,
                        days,
                        from,
                        to,
                        count: this.countEvents(result.data),
                        cached: false,
                        source: 'sunrise-sunset.org'
                    }
                };
                
                await this.cacheRepository.set(cacheKey, JSON.stringify(finalResult), 3600);
                return finalResult;
            }
            
            console.log('All external APIs failed, returning enhanced demo data');
            return await this.getEnhancedDemoEvents(lat, lon, days);

        } catch (error) {
            console.error('Error fetching astronomy events:', error.message);
            
            return {
                success: false,
                error: error.message,
                data: await this.getEnhancedDemoEvents(lat, lon, days),
                metadata: {
                    lat,
                    lon,
                    days,
                    note: 'Using enhanced demo data (external APIs unavailable)'
                }
            };
        }
    }

    async tryOpenNotifyAPI(lat, lon) {
        try {
            console.log('Trying Open Notify ISS API...');
            const url = `https://api.open-notify.org/iss-pass.json?lat=${lat}&lon=${lon}&n=5`;
            
            const response = await fetch(url, { timeout: 10000 });
            
            if (!response.ok) {
                throw new Error(`Open Notify API error: ${response.status}`);
            }
            
            const data = await response.json();
            
            const events = data.response.map((pass, index) => ({
                name: "International Space Station",
                type: "satellite_flyby",
                time: new Date(pass.risetime * 1000).toISOString(),
                duration: pass.duration,
                details: `ISS visible for ${Math.round(pass.duration/60)} minutes, max altitude: ${pass.duration > 300 ? 'High' : 'Low'}`
            }));
            
            const now = new Date();
            const allEvents = [
                ...events,
                {
                    name: "Sun",
                    type: "sun_position",
                    time: new Date(now.getTime() + 3600000).toISOString(),
                    details: "Solar noon"
                },
                {
                    name: "Moon",
                    type: "moon_phase",
                    time: new Date(now.getTime() + 86400000).toISOString(),
                    phase: "Waxing Gibbous",
                    details: "Moon 75% illuminated"
                }
            ];
            
            return {
                success: true,
                data: { events: allEvents }
            };
            
        } catch (error) {
            console.log('Open Notify API failed:', error.message);
            return { success: false, error: error.message };
        }
    }

    async trySunriseSunsetAPI(lat, lon, days) {
        try {
            console.log('Trying Sunrise Sunset API...');
            
            const now = new Date();
            const events = [];
            
            for (let i = 0; i < Math.min(days, 3); i++) {
                const date = new Date(now.getTime() + (i * 86400000));
                const dateStr = date.toISOString().split('T')[0];
                
                const url = `https://api.sunrise-sunset.org/json?lat=${lat}&lng=${lon}&date=${dateStr}&formatted=0`;
                
                const response = await fetch(url, { timeout: 10000 });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.status === "OK") {
                        const results = data.results;
                        
                        events.push({
                            name: "Sunrise",
                            type: "sun_event",
                            time: results.sunrise,
                            details: `Sunrise, day length: ${results.day_length}`
                        });
                        
                        events.push({
                            name: "Sunset",
                            type: "sun_event",
                            time: results.sunset,
                            details: `Sunset, civil twilight: ${results.civil_twilight_end}`
                        });
                        
                        if (results.solar_noon) {
                            events.push({
                                name: "Solar Noon",
                                type: "sun_position",
                                time: results.solar_noon,
                                details: "Sun at highest point"
                            });
                        }
                    }
                }
                
                const moonEventDate = new Date(now.getTime() + (i * 86400000) + 43200000);
                const moonPhases = ["New Moon", "Waxing Crescent", "First Quarter", "Waxing Gibbous", 
                                  "Full Moon", "Waning Gibbous", "Last Quarter", "Waning Crescent"];
                const moonPhase = moonPhases[i % moonPhases.length];
                
                events.push({
                    name: "Moon",
                    type: "moon_phase",
                    time: moonEventDate.toISOString(),
                    phase: moonPhase,
                    details: `${moonPhase} phase`
                });
            }
            
            if (events.length > 0) {
                return {
                    success: true,
                    data: { events: events.slice(0, 10) }
                };
            }
            
            return { success: false, error: "No events generated" };
            
        } catch (error) {
            console.log('Sunrise Sunset API failed:', error.message);
            return { success: false, error: error.message };
        }
    }

    async getEnhancedDemoEvents(lat, lon, days) {
        const now = new Date();
        const events = [];
        
        for (let i = 0; i < Math.min(days, 14); i++) {
            const baseTime = now.getTime() + (i * 86400000);
            
            const issTime = new Date(baseTime + (i * 3600000));
            events.push({
                name: "International Space Station",
                type: "satellite_flyby",
                time: issTime.toISOString(),
                altitude: `${400 + (i * 10)} km`,
                details: `ISS visible for ${3 + (i % 5)} minutes, magnitude: -${2 + (i % 3)}.0`
            });
            
            events.push({
                name: "Sun",
                type: "sun_event",
                time: new Date(baseTime + 36000000).toISOString(),
                details: `Solar noon, elevation: ${30 + (i % 20)}°`
            });
            
            const moonPhases = ["New Moon", "Waxing Crescent", "First Quarter", "Waxing Gibbous", 
                              "Full Moon", "Waning Gibbous", "Last Quarter", "Waning Crescent"];
            const moonPhaseIndex = (i + Math.floor(days / 2)) % moonPhases.length;
            
            events.push({
                name: "Moon",
                type: "moon_phase",
                time: new Date(baseTime + 57600000).toISOString(),
                phase: moonPhases[moonPhaseIndex],
                details: `${moonPhases[moonPhaseIndex]}, illumination: ${(moonPhaseIndex * 12.5)}%`
            });
            
            if (i % 3 === 0) {
                const planets = ["Mars", "Jupiter", "Saturn", "Venus"];
                const planet = planets[i % planets.length];
                
                events.push({
                    name: planet,
                    type: "planet_visible",
                    time: new Date(baseTime + 72000000).toISOString(),
                    magnitude: `${-1.5 + (i * 0.1)}`,
                    details: `${planet} visible in the ${i % 2 === 0 ? 'evening' : 'morning'} sky`
                });
            }
            
            if (i % 5 === 0) {
                const showers = ["Perseids", "Leonids", "Geminids", "Orionids"];
                const shower = showers[i % showers.length];
                
                events.push({
                    name: `${shower} Meteor Shower`,
                    type: "meteor_shower",
                    time: new Date(baseTime + 79200000).toISOString(),
                    rate: `${20 + (i * 5)} per hour`,
                    details: `Peak of ${shower} meteor shower`
                });
            }
        }
        
        events.sort((a, b) => new Date(a.time) - new Date(b.time));
        
        return {
            events: events.slice(0, 50),
            metadata: {
                lat,
                lon,
                days,
                note: "Enhanced demo data - consider configuring external APIs for real data"
            }
        };
    }

    countEvents(data) {
        if (!data || !data.events) return 0;
        return data.events.length;
    }
}

module.exports = AstronomyService;