document.addEventListener(
    "DOMContentLoaded",
    function () {

        const forms =
            document.querySelectorAll(
                "form"
            );

        forms.forEach(function (form) {

            form.addEventListener(
                "submit",
                function (event) {

                    const price =
                        form.querySelector(
                            "[name='unit_price']"
                        );

                    const quantity =
                        form.querySelector(
                            "[name='quantity']"
                        );

                    if (price && quantity) {

                        if (
                            parseFloat(
                                price.value
                            ) < 0
                        ) {

                            event.preventDefault();

                            alert(
                                "Unit price cannot be negative."
                            );

                            return;
                        }

                        if (
                            parseInt(
                                quantity.value
                            ) < 0
                        ) {

                            event.preventDefault();

                            alert(
                                "Quantity cannot be negative."
                            );
                        }
                    }

                }
            );

        });

    }
);