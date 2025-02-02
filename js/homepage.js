angular.module('farmApp', [])
            .controller('MainController', ['$scope', function ($scope) {
                $scope.currentPage = 'home';
                $scope.cart = [];
                $scope.showUserProducts = false;
                $scope.showFarmerProducts = false;

                $scope.products = {
                    users: {
                        vegetables: [
                            

            { name: 'Tomato', price: 40, image: 'tomtos.jpg' },
            { name: 'Potato', price: 30, image: 'potato.jpg' },
            { name: 'Carrots', price: 60, image: 'carrots.jpg' },
            { name: 'Capsicum', price: 60, image: 'capsicum1.jpeg' },
            { name: 'onion', price: 60, image: 'onion.jpeg' },
            { name: 'beetroots', price: 60, image: 'beetroot-1.jpg' }, 
            { name: 'spinach', price: 60, image: 'spinach.jpg' },
            { name: 'Fenugreek (Methi)', price: 60, image: 'MethiFenugreek.jpg' },
            { name: 'ladyfinger', price: 60, image: 'ladyfinger.jpg' },
            { name: 'cabage', price: 60, image: 'cabage.jpeg' },

                        ],
                        fruits: [
                            
            { name: 'Apple', price: 120, image: 'apple.jpg' },
            { name: 'Banana', price: 50, image: 'banana.jpg' },
            { name: 'Watermelon', price: 45, image: 'watermelons.jpg' },
            { name: 'Strawberries', price: 150, image: 'strawberies.png' },
            { name: 'Grapes', price: 60, image: 'grapes.jpg' }

                        ],
                    },
                    farmers: {
                        
                        Seeds: [
            { name: 'Watermelons seeds', price: 500, image: 'watermelonseed.jpg' }, 
            { name: 'Tomatos seeds', price: 450, image: 'tomatoseeds.jpg' },
            { name: 'Coriender seeds', price: 450, image: 'coriender.jpg' },
            { name: 'Cabage seeds', price: 450, image: 'cabage.jpg' }
            
        ],
        Machinery: [
            { name: 'Tractor', price: 500000, image: 'tractor.jpg' },
            { name: 'Plough', price: 4500, image: 'plough.png' },
            { name: 'knapsack', price: 4500, image: 'knapsack.jpg' },
            { name: 'Sickle', price: 4500, image: 'khurpa1.jpg' },
            { name: 'Pickaxe', price: 4500, image: 'pickaxes.png' }
        ],
        Fertilizers: [
            { name: 'Vegetable Fertilizer', price: 500, image: 'vegf.jpg' },
            { name: 'Fruit Fertilizer', price: 450, image: 'fruit.jpg' },
            { name: 'Organic fertilizer', price: 450, image: 'organic.jpg' },
            { name: ' Ammonium sulphate (chemical fertilizer)', price: 450, image: 'chemicalf.jpg' },
            

        ], 
                    },
                    
                };

                $scope.sellProductName = '';
                $scope.sellProductPrice = '';
                $scope.sellProductCategory = '';
                $scope.sellProductImage = null;

                $scope.selectedUserCategory = '';
                $scope.selectedFarmerCategory = '';

                $scope.addToCart = function (product) {
    let found = $scope.cart.find(item => item.name === product.name);
    if (found) {
        found.quantity += 1;
    } else {
        $scope.cart.push({ ...product, quantity: 1 });
    }
    alert(product.name + ' has been added to your cart!');
};
$scope.increaseQuantity = function (product) {
    product.quantity += 1;
};

$scope.decreaseQuantity = function (product) {
    if (product.quantity > 1) {
        product.quantity -= 1;
    } else {
        $scope.removeFromCart(product);
    }
};
$scope.getTotalPrice = function () {
    return $scope.cart.reduce((total, product) => total + (product.price * product.quantity), 0);
};


                $scope.removeFromCart = function (product) {
                    let index = $scope.cart.indexOf(product);
                    if (index > -1) {
                        $scope.cart.splice(index, 1);
                    }
                };

                $scope.navigate = function (page) {
                    $scope.currentPage = page;
                };

                $scope.toggleUserProducts = function () {
                    $scope.showUserProducts = !$scope.showUserProducts;
                    $scope.showFarmerProducts = false;
                };

                $scope.toggleFarmerProducts = function () {
                    $scope.showFarmerProducts = !$scope.showFarmerProducts;
                    $scope.showUserProducts = false;
                };

                $scope.selectUserCategory = function (category) {
                    $scope.selectedUserCategory = category;
                };

                $scope.selectFarmerCategory = function (category) {
                    $scope.selectedFarmerCategory = category;
                };

                $scope.showCheckoutForm = function (product) {
                    $scope.selectedProduct = product;
                    $scope.showCheckout = true;
                };

                $scope.cancelCheckout = function () {
                    $scope.showCheckout = false;
                };

                $scope.confirmPurchase = function () {
                    alert('Purchase Confirmed!');
                    $scope.showCheckout = false;
                    $scope.cart = [];
                };

                $scope.sellProduct = function () {
    if ($scope.sellProductName && $scope.sellProductPrice && $scope.sellProductCategory && $scope.sellProductImage) {
        let newProduct = {
            name: $scope.sellProductName,
            price: $scope.sellProductPrice,
            image: URL.createObjectURL($scope.sellProductImage),
        };

        if (!$scope.products.users.productsByFarmers) {
            $scope.products.users.productsByFarmers = [];
        }

        $scope.products.users.productsByFarmers.push(newProduct);

        alert('Product Listed for Sale!');
        $scope.sellProductName = '';
        $scope.sellProductPrice = '';
        $scope.sellProductCategory = '';
        $scope.sellProductImage = null;
    } else {
        alert('Please fill all details and upload an image');
    }
};


                $scope.register = function () {
    // Validation: Check if all required fields are filled
    if (!$scope.registerName || !$scope.registerEmail || !$scope.registerPassword || 
        ($scope.registerRole === 'farmer' && !$scope.registerFarmName)) {
        alert('Please fill all the required details');
    } else {
        alert('Registration Successful!');
        $scope.navigate('login');
    }
};

//=======================================================================================================
// Updated Code for Login
//=======================================================================================================

 // Hardcoded Admin Credentials
 const adminCredentials = {
    email: 'admin@example.com',
    password: 'admin123'
};

$scope.login = function () {
    if (!$scope.loginEmail || !$scope.loginPassword) {
        alert('Please enter valid email and password.');
        return;
    }

    if ($scope.loginRole === 'admin') {
        if ($scope.loginEmail === adminCredentials.email && $scope.loginPassword === adminCredentials.password) {
            alert('Admin Login Successful!');
            window.location.href = './pages/adminportal.html'; // Check if path is correct
        } else {
            alert('Invalid Admin Credentials');
        }
    } else if ($scope.loginRole === 'user' || $scope.loginRole === 'farmer') {
        // For now, just validate non-empty fields
        if ($scope.loginEmail && $scope.loginPassword) {
            alert($scope.loginRole.charAt(0).toUpperCase() + $scope.loginRole.slice(1) + ' Login Successful!');
            $scope.currentPage = 'home'; // You can navigate to the homepage or dashboard
        } else {
            alert('Invalid credentials for ' + $scope.loginRole);
        }
    } else {
        alert('Please select a valid role');
    }
};


//=======================================================================================================
//=======================================================================================================
$scope.crops = {
            kharif: [
                { name: 'Rice', image: 'images/rice.jpg', link: 'rice.html' },
                { name: 'Maize', image: 'images/maize.jpg', link: 'crops/maize.html' }
            ],
            rabi: [
                { name: 'Wheat', image: 'images/wheat.jpg', link: 'crops/wheat.html' },
                { name: 'Barley', image: 'images/barley.jpg', link: 'crops/barley.html' }
            ]
        };
        // Select category
        $scope.selectCategory = function (category) {
            $scope.selectedCategory = category;
        };

        // Open Crop Details Page
        $scope.openCropPage = function (link) {
            window.location.href = link;
        };
        $scope.subsidies = [
    {
        name: 'PM Kisan Samman Nidhi',
        amount: 6000,
        description: 'Financial support for farmers',
        eligibility: 'Small & marginal farmers',
        category: 'Direct Benefit Transfer',
        documents: 'Aadhar, Bank Passbook, Land Record',
    },
    {
        name: 'Agriculture Infrastructure Fund',
        amount: 200000,
        description: 'Support for agricultural infrastructure',
        eligibility: 'Farmers, FPOs, SHGs',
        category: 'Loan Subsidy',
        documents: 'Aadhar, Business Plan, Bank Details',
    }
];

$scope.showSubsidyForm = false;

$scope.applyForSubsidy = function(subsidy) {
    $scope.selectedSubsidy = subsidy;
    $scope.showSubsidyForm = true;
};

$scope.closeSubsidyForm = function () {
    $scope.showSubsidyForm = false;
};

$scope.submitSubsidyApplication = function() {
    alert('Your application for ' + $scope.selectedSubsidy.name + ' has been submitted successfully!');
    $scope.showSubsidyForm = false;
};


            }]);