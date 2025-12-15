const axios = require('axios');

class JwstClient {
    constructor() {
        this.baseURL = process.env.JWST_HOST || 'https://api.jwstapi.com';
        this.apiKey = process.env.JWST_API_KEY;
        this.email = process.env.JWST_EMAIL;

        this.client = axios.create({
            baseURL: this.baseURL,
            timeout: 30000,
            headers: {
                'x-api-key': this.apiKey,
                'X-API-Email': this.email,
                'Content-Type': 'application/json'
            }
        });

        console.log('JWST Client initialized. BaseURL:', this.baseURL);
    }

    async _handleRequest(requestPromise) {
        try {
            const response = await requestPromise;
            return {
                success: true,
                data: response.data,
                status: response.status,
                headers: response.headers
            };
        } catch (error) {
            console.error('JWST API Request Failed:');
            
            if (error.response) {
                console.error(`Status: ${error.response.status}`);
                console.error(`Data: ${JSON.stringify(error.response.data)}`);
                return {
                    success: false,
                    error: `API Error ${error.response.status}: ${error.response.data?.error || 'Unknown server error'}`,
                    status: error.response.status
                };
            } else if (error.request) {
                console.error('No response received:', error.request);
                return {
                    success: false,
                    error: 'Network error: No response from JWST API server'
                };
            } else {
                console.error('Request setup error:', error.message);
                return {
                    success: false,
                    error: `Request error: ${error.message}`
                };
            }
        }
    }

    async getImages(params = {}) {
        const { type = 'jpg', page = 1, perPage = 24, program, instrument } = params;
        console.log(`Fetching images. Type: ${type}, Page: ${page}, PerPage: ${perPage}`);

        const apiResult = await this._handleRequest(
            this.client.get(`/all/type/${type}`, {
                params: { page, perPage }
            })
        );

        if (!apiResult.success) {
            return apiResult;
        }

        let items = apiResult.data?.body || [];
        const totalFromApi = apiResult.data?.body?.length || 0;

        if (program) {
            items = items.filter(item => item.program == program);
        }
        if (instrument) {
            items = items.filter(item =>
                item.details?.instruments?.some(i => i.instrument === instrument)
            );
        }

        const startIndex = (page - 1) * perPage;
        const paginatedItems = items.slice(startIndex, startIndex + perPage);

        const result = {
            success: true,
            data: {
                items: paginatedItems,
                total: items.length,
                page: page,
                limit: perPage,
                hasMore: startIndex + perPage < items.length
            },
            meta: {
                source: 'api.jwstapi.com',
                unfilteredTotal: totalFromApi
            }
        };

        return result;
    }

    async getAllImages(type = 'jpg', limit = 100) {
        console.log(`Fetching all images for caching. Type: ${type}, Limit: ${limit}`);
        const result = await this.getImages({ type, perPage: limit, page: 1 });
        
        return {
            success: result.success,
            data: result.success ? { body: result.data.items } : null,
            status: result.status
        };
    }

    async getInstruments() {
        console.log('Fetching data to analyze instruments...');
        const result = await this.getImages({ perPage: 100, page: 1 });

        if (!result.success) {
            return { success: false, error: result.error, data: {} };
        }

        const instruments = {};
        const items = result.data.items;

        items.forEach(item => {
            item.details?.instruments?.forEach(instr => {
                const name = instr.instrument;
                instruments[name] = (instruments[name] || 0) + 1;
            });
        });

        return {
            success: true,
            data: instruments
        };
    }

    async checkHealth() {
        console.log('Checking JWST API health...');
        try {
            const testResult = await this.getImages({ perPage: 1, page: 1 });
            return {
                connected: testResult.success,
                status: testResult.status,
                message: testResult.success ? 'API is operational' : testResult.error
            };
        } catch (error) {
            console.error('Health check failed:', error);
            return {
                connected: false,
                error: error.message,
                message: 'Health check request failed'
            };
        }
    }
}

module.exports = JwstClient;