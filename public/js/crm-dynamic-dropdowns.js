/**
 * CRM Dynamic Dropdowns
 * Populates form dropdowns using API endpoints for better maintainability
 * Beginner-friendly with error handling and fallbacks
 */

class CRMDropdowns {
    constructor() {
        this.cache = new Map();
        this.init();
    }

    init() {
        // Initialize dropdowns when DOM is ready
        $(document).ready(() => {
            console.log('CRM Dropdowns: Initializing...');
            this.populateAllDropdowns();
        });
    }

    // Generic method to fetch and cache data with better error handling
    async fetchLookupData(type) {
        if (this.cache.has(type)) {
            console.log(`CRM Dropdowns: Using cached data for ${type}`);
            return this.cache.get(type);
        }

        console.log(`CRM Dropdowns: Fetching ${type} from API...`);
        
        try {
            const response = await fetch(`/api/lookups/${type}`);
            
            if (!response.ok) {
                console.warn(`CRM Dropdowns: API returned ${response.status} for ${type}, using fallback`);
                return this.getFallbackData(type);
            }
            
            const data = await response.json();
            
            if (!Array.isArray(data) || data.length === 0) {
                console.warn(`CRM Dropdowns: No data received for ${type}, using fallback`);
                return this.getFallbackData(type);
            }
            
            this.cache.set(type, data);
            console.log(`CRM Dropdowns: Successfully loaded ${data.length} items for ${type}`);
            return data;
            
        } catch (error) {
            console.error(`CRM Dropdowns: Error fetching ${type}:`, error);
            console.log(`CRM Dropdowns: Using fallback data for ${type}`);
            return this.getFallbackData(type);
        }
    }

    // Fallback data for when API fails
    getFallbackData(type) {
        const fallbacks = {
            'sources': [
                {id: 1, name: 'Website'},
                {id: 2, name: 'Referral'},
                {id: 3, name: 'Social Media'},
                {id: 4, name: 'Email'},
                {id: 5, name: 'Phone'}
            ],
            'lead-statuses': [
                {id: 1, name: 'New'},
                {id: 2, name: 'Contacted'},
                {id: 3, name: 'Qualified'},
                {id: 4, name: 'Converted'},
                {id: 5, name: 'Lost'}
            ],
            'interaction-types': [
                {id: 1, name: 'Call'},
                {id: 2, name: 'Email'},
                {id: 3, name: 'Meeting'},
                {id: 4, name: 'Note'},
                {id: 5, name: 'Task'}
            ],
            'task-statuses': [
                {id: 1, name: 'pending'},
                {id: 2, name: 'in_progress'},
                {id: 3, name: 'completed'},
                {id: 4, name: 'cancelled'}
            ],
            'users': [
                {id: 1, name: 'Admin User', email: 'admin@example.com'},
                {id: 2, name: 'Sales Manager', email: 'sales@example.com'}
            ],
            'leads': [
                {id: 1, name: 'Sample Lead', company: 'Sample Company'},
                {id: 2, name: 'Test Lead', company: 'Test Company'}
            ]
        };
        
        return fallbacks[type] || [];
    }

    // Populate a single dropdown with better visual feedback
    async populateDropdown(selector, data, options = {}) {
        const {
            valueField = 'id',
            labelField = 'name',
            placeholder = 'Select...',
            addBlank = true,
            selectedValue = null,
            type = 'default'
        } = options;

        const $select = $(selector);
        if (!$select.length) {
            console.warn(`CRM Dropdowns: Dropdown not found: ${selector}`);
            return;
        }

        // Show loading state
        $select.addClass('loading');
        const originalPlaceholder = $select.find('option:first').text();
        $select.find('option:first').text('Loading...');

        // Clear existing options
        $select.find('option:not(:first)').remove();

        try {
            // Add blank option if requested
            if (addBlank) {
                const $blank = $('<option>')
                    .val('')
                    .text(placeholder);
                $select.prepend($blank);
            }

            // Add data options
            data.forEach(item => {
                const value = item[valueField];
                const label = item[labelField];
                
                // Format label with additional fields if available
                let displayLabel = label;
                if (item.company && type === 'leads') {
                    displayLabel = `${label} (${item.company})`;
                }
                if (item.email && type === 'users') {
                    displayLabel = `${label} (${item.email})`;
                }

                const $option = $('<option>')
                    .val(value)
                    .text(displayLabel);
                
                if (selectedValue == value) {
                    $option.prop('selected', true);
                }
                
                $select.append($option);
            });

            // Show success feedback
            if (data.length > 0) {
                console.log(`CRM Dropdowns: Successfully populated ${selector} with ${data.length} options`);
            } else {
                console.warn(`CRM Dropdowns: No data available for ${selector}`);
                $select.append($('<option value="">No options available</option>'));
            }

        } catch (error) {
            console.error(`CRM Dropdowns: Error populating ${selector}:`, error);
            $select.append($('<option value="">Error loading options</option>'));
        } finally {
            // Remove loading state
            $select.removeClass('loading');
            // Trigger change event for any dependent logic
            $select.trigger('change');
        }
    }

    // Populate all dropdowns on the page
    async populateAllDropdowns() {
        // Lead dropdowns
        this.populateLeadDropdowns();
        
        // User dropdowns
        this.populateUserDropdowns();
        
        // Status and type dropdowns
        this.populateStatusDropdowns();
        
        // Source dropdowns
        this.populateSourceDropdowns();
        
        // Designation dropdowns
        this.populateDesignationDropdowns();
    }

    async populateLeadDropdowns() {
        try {
            const leads = await this.fetchLookupData('leads');
            
            // Populate lead selection dropdowns
            $('select[name="lead_id"]').each((i, select) => {
                const selectedValue = $(select).data('selected') || null;
                this.populateDropdown(select, leads, {
                    placeholder: 'Select Lead...',
                    selectedValue: selectedValue,
                    type: 'leads'
                });
            });
        } catch (error) {
            console.error('CRM Dropdowns: Error in populateLeadDropdowns:', error);
        }
    }

    async populateUserDropdowns() {
        try {
            const users = await this.fetchLookupData('users');
            
            // Populate user assignment dropdowns
            $('select[name="user_id"], select[name="assigned_to"]').each((i, select) => {
                const selectedValue = $(select).data('selected') || null;
                this.populateDropdown(select, users, {
                    labelField: 'name',
                    placeholder: 'Select User...',
                    selectedValue: selectedValue,
                    type: 'users'
                });
            });
        } catch (error) {
            console.error('CRM Dropdowns: Error in populateUserDropdowns:', error);
        }
    }

    async populateStatusDropdowns() {
        try {
            // Lead statuses
            const leadStatuses = await this.fetchLookupData('lead-statuses');
            $('select[name="status_id"]').each((i, select) => {
                // Check if this is a lead form (based on context or parent form)
                const $form = $(select).closest('form');
                const isLeadForm = $form.find('select[name="source_id"], select[name="assigned_to"]').length > 0;
                
                if (isLeadForm) {
                    const selectedValue = $(select).data('selected') || null;
                    this.populateDropdown(select, leadStatuses, {
                        placeholder: 'Select Status...',
                        selectedValue: selectedValue,
                        type: 'lead-statuses'
                    });
                }
            });

            // Task statuses - handle both name patterns
            const taskStatuses = await this.fetchLookupData('task-statuses');
            $('select[name="status_id"]').each((i, select) => {
                const $form = $(select).closest('form');
                const isTaskForm = $form.find('select[name="lead_id"], select[name="priority"]').length > 0;
                
                if (isTaskForm) {
                    const selectedValue = $(select).data('selected') || null;
                    this.populateDropdown(select, taskStatuses, {
                        labelField: 'name',
                        placeholder: 'Select Task Status...',
                        selectedValue: selectedValue,
                        type: 'task-statuses'
                    });
                }
            });

            // Interaction types
            const interactionTypes = await this.fetchLookupData('interaction-types');
            $('select[name="interaction_type_id"]').each((i, select) => {
                const selectedValue = $(select).data('selected') || null;
                this.populateDropdown(select, interactionTypes, {
                    labelField: 'name',
                    placeholder: 'Select Interaction Type...',
                    selectedValue: selectedValue,
                    type: 'interaction-types'
                });
            });
        } catch (error) {
            console.error('CRM Dropdowns: Error in populateStatusDropdowns:', error);
        }
    }

    async populateSourceDropdowns() {
        try {
            const sources = await this.fetchLookupData('sources');
            
            $('select[name="source_id"]').each((i, select) => {
                const selectedValue = $(select).data('selected') || null;
                this.populateDropdown(select, sources, {
                    placeholder: 'Select Source...',
                    selectedValue: selectedValue,
                    type: 'sources'
                });
            });
        } catch (error) {
            console.error('CRM Dropdowns: Error in populateSourceDropdowns:', error);
        }
    }

    async populateDesignationDropdowns() {
        try {
            const designations = await this.fetchLookupData('designations');
            
            $('select[name="designation_id"]').each((i, select) => {
                const selectedValue = $(select).data('selected') || null;
                this.populateDropdown(select, designations, {
                    placeholder: 'Select Designation...',
                    selectedValue: selectedValue,
                    type: 'designations'
                });
            });
        } catch (error) {
            console.error('CRM Dropdowns: Error in populateDesignationDropdowns:', error);
        }
    }

    // Refresh specific dropdown type
    async refreshDropdown(type) {
        switch(type) {
            case 'leads':
                await this.populateLeadDropdowns();
                break;
            case 'users':
                await this.populateUserDropdowns();
                break;
            case 'statuses':
                await this.populateStatusDropdowns();
                break;
            case 'sources':
                await this.populateSourceDropdowns();
                break;
            case 'designations':
                await this.populateDesignationDropdowns();
                break;
        }
    }

    // Clear cache and refresh all dropdowns
    async refreshAll() {
        this.cache.clear();
        await this.populateAllDropdowns();
    }
}

// Initialize the dropdown system
window.crmDropdowns = new CRMDropdowns();

// Global function for manual refresh (can be called from other scripts)
window.refreshCRMDropdowns = async (type = null) => {
    if (type) {
        await window.crmDropdowns.refreshDropdown(type);
    } else {
        await window.crmDropdowns.refreshAll();
    }
};
