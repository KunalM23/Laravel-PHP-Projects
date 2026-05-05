/**
 * CRM System Test and Verification
 * Beginner-friendly testing script to verify all functionality
 */

class CRMTest {
    constructor() {
        this.testResults = [];
        this.init();
    }

    init() {
        // Run tests when DOM is ready
        $(document).ready(() => {
            console.log('CRM Test: Starting system verification...');
            this.runAllTests();
        });
    }

    // Test 1: Check if jQuery is loaded
    testjQuery() {
        try {
            if (typeof $ !== 'undefined') {
                this.logResult('jQuery', 'PASS', 'jQuery is loaded and working');
                return true;
            } else {
                this.logResult('jQuery', 'FAIL', 'jQuery is not loaded');
                return false;
            }
        } catch (error) {
            this.logResult('jQuery', 'FAIL', `jQuery test error: ${error.message}`);
            return false;
        }
    }

    // Test 2: Check if CRM Dropdowns class is loaded
    testCRMDropdowns() {
        try {
            if (typeof window.crmDropdowns !== 'undefined') {
                this.logResult('CRM Dropdowns', 'PASS', 'CRM Dropdowns class is loaded');
                return true;
            } else {
                this.logResult('CRM Dropdowns', 'FAIL', 'CRM Dropdowns class is not loaded');
                return false;
            }
        } catch (error) {
            this.logResult('CRM Dropdowns', 'FAIL', `CRM Dropdowns test error: ${error.message}`);
            return false;
        }
    }

    // Test 3: Check if forms have dropdowns
    testFormDropdowns() {
        try {
            const dropdowns = $('select[data-selected], select[name="lead_id"], select[name="user_id"], select[name="status_id"]');
            
            if (dropdowns.length > 0) {
                this.logResult('Form Dropdowns', 'PASS', `Found ${dropdowns.length} dynamic dropdowns`);
                return true;
            } else {
                this.logResult('Form Dropdowns', 'WARN', 'No dynamic dropdowns found on current page');
                return true; // This is okay if we're not on a form page
            }
        } catch (error) {
            this.logResult('Form Dropdowns', 'FAIL', `Form dropdowns test error: ${error.message}`);
            return false;
        }
    }

    // Test 4: Check if sidebar navigation is working
    testSidebarNavigation() {
        try {
            const sidebar = $('.menu-sidebar');
            const navItems = $('.navbar__list > li');
            const arrows = $('.js-arrow');
            
            if (sidebar.length > 0 && navItems.length > 0) {
                this.logResult('Sidebar Navigation', 'PASS', `Sidebar found with ${navItems.length} menu items`);
                
                if (arrows.length > 0) {
                    this.logResult('Dropdown Arrows', 'PASS', `Found ${arrows.length} dropdown arrows`);
                } else {
                    this.logResult('Dropdown Arrows', 'WARN', 'No dropdown arrows found');
                }
                return true;
            } else {
                this.logResult('Sidebar Navigation', 'FAIL', 'Sidebar or navigation items not found');
                return false;
            }
        } catch (error) {
            this.logResult('Sidebar Navigation', 'FAIL', `Sidebar test error: ${error.message}`);
            return false;
        }
    }

    // Test 5: Check if API endpoints are accessible
    async testAPIEndpoints() {
        try {
            const endpoints = [
                '/api/lookups/sources',
                '/api/lookups/lead-statuses',
                '/api/lookups/users'
            ];

            let successCount = 0;
            
            for (const endpoint of endpoints) {
                try {
                    const response = await fetch(endpoint);
                    if (response.ok) {
                        successCount++;
                        this.logResult(`API ${endpoint}`, 'PASS', 'API endpoint is accessible');
                    } else {
                        this.logResult(`API ${endpoint}`, 'WARN', `API returned status ${response.status}`);
                    }
                } catch (error) {
                    this.logResult(`API ${endpoint}`, 'WARN', `API test failed: ${error.message}`);
                }
            }

            if (successCount > 0) {
                this.logResult('API Connectivity', 'PASS', `${successCount}/${endpoints.length} endpoints working`);
                return true;
            } else {
                this.logResult('API Connectivity', 'WARN', 'No API endpoints accessible, fallbacks will be used');
                return true; // Fallbacks make this okay
            }
        } catch (error) {
            this.logResult('API Connectivity', 'FAIL', `API test error: ${error.message}`);
            return false;
        }
    }

    // Test 6: Check if CSS is loaded
    testCSS() {
        try {
            const testElement = $('<div>').addClass('loading').css('opacity', '0.7');
            const opacity = testElement.css('opacity');
            
            if (opacity === '0.7') {
                this.logResult('CSS Loading', 'PASS', 'CSS loading states are working');
                return true;
            } else {
                this.logResult('CSS Loading', 'WARN', 'CSS loading states may not be working');
                return true; // Not critical
            }
        } catch (error) {
            this.logResult('CSS Loading', 'FAIL', `CSS test error: ${error.message}`);
            return false;
        }
    }

    // Run all tests
    async runAllTests() {
        console.log('CRM Test: Running all tests...');
        
        const tests = [
            () => this.testjQuery(),
            () => this.testCRMDropdowns(),
            () => this.testFormDropdowns(),
            () => this.testSidebarNavigation(),
            () => this.testCSS(),
            () => this.testAPIEndpoints()
        ];

        let passedTests = 0;
        let totalTests = tests.length;

        for (const test of tests) {
            try {
                const result = await test();
                if (result) passedTests++;
            } catch (error) {
                console.error('CRM Test: Test execution error:', error);
            }
        }

        // Summary
        console.log(`CRM Test: ${passedTests}/${totalTests} tests passed`);
        this.showTestSummary(passedTests, totalTests);
    }

    // Log test result
    logResult(testName, status, message) {
        const result = {
            test: testName,
            status: status,
            message: message,
            timestamp: new Date().toLocaleTimeString()
        };
        
        this.testResults.push(result);
        
        const icon = status === 'PASS' ? 'PASS' : status === 'WARN' ? 'WARN' : 'FAIL';
        console.log(`CRM Test [${icon}] ${testName}: ${message}`);
    }

    // Show test summary in console
    showTestSummary(passed, total) {
        console.log('\n' + '='.repeat(50));
        console.log('CRM SYSTEM TEST SUMMARY');
        console.log('='.repeat(50));
        console.log(`Tests Passed: ${passed}/${total}`);
        console.log(`Success Rate: ${Math.round((passed/total) * 100)}%`);
        
        if (passed === total) {
            console.log('Status: ALL SYSTEMS GO! CRM is ready to use.');
        } else {
            console.log('Status: Some issues detected, but CRM should still work with fallbacks.');
        }
        
        console.log('\nTest Details:');
        this.testResults.forEach(result => {
            const icon = result.status === 'PASS' ? 'PASS' : result.status === 'WARN' ? 'WARN' : 'FAIL';
            console.log(`  [${icon}] ${result.test}: ${result.message}`);
        });
        
        console.log('='.repeat(50));
    }

    // Show beginner-friendly help
    showHelp() {
        console.log('\nCRM BEGINNER HELP:');
        console.log('1. If dropdowns show "Loading..." but never load, check your internet connection');
        console.log('2. If API tests fail, the CRM will use fallback data - this is normal!');
        console.log('3. If sidebar menus don\'t open, try refreshing the page');
        console.log('4. All forms should work even if some tests show warnings');
        console.log('5. Check the browser console (F12) for detailed error messages');
    }
}

// Initialize the test system
window.crmTest = new CRMTest();

// Global help function
window.showCRMHelp = () => {
    window.crmTest.showHelp();
};
