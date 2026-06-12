const ugandaLocations = {
    regions: {
        "Central": ["Kampala", "Wakiso", "Mukono", "Mpigi", "Masaka"],
        "Eastern": ["Jinja", "Mbale", "Iganga", "Tororo", "Soroti"],
        "Northern": ["Gulu", "Lira", "Kitgum", "Arua", "Nebbi"],
        "Western": ["Mbarara", "Kasese", "Bushenyi", "Hoima", "Kabale"]
    },
    districts: {
        "Kampala": {
            counties: ["Kampala Central", "Kawempe", "Makindye", "Nakawa", "Rubaga"],
            subcounties: {
                "Kampala Central": {
                    "Central Division": ["Nakasero", "Kololo", "Mengo"],
                    "Kololo": ["Kololo I", "Kololo II", "Kololo III"],
                    "Nakasero": ["Nakasero I", "Nakasero II", "Nakasero III"]
                },
                "Kawempe": {
                    "Kawempe Division": ["Kawempe I", "Kawempe II", "Bwaise"],
                    "Makerere": ["Makerere I", "Makerere II", "Makerere III"],
                    "Mulago": ["Mulago I", "Mulago II", "Wandegeya"]
                },
                "Makindye": {
                    "Makindye Division": ["Makindye I", "Makindye II", "Kabalagala"],
                    "Kibuye": ["Kibuye I", "Kibuye II", "Ndeeba"],
                    "Nsambya": ["Nsambya I", "Nsambya II", "Ggaba"]
                },
                "Nakawa": {
                    "Nakawa Division": ["Nakawa I", "Nakawa II", "Bugolobi"],
                    "Butabika": ["Butabika I", "Butabika II", "Luzira"],
                    "Ntinda": ["Ntinda I", "Ntinda II", "Kisasi"]
                },
                "Rubaga": {
                    "Rubaga Division": ["Rubaga I", "Rubaga II", "Lungujja"],
                    "Namirembe": ["Namirembe I", "Namirembe II", "Kasubi"],
                    "Ndeeba": ["Ndeeba I", "Ndeeba II", "Mutundwe"]
                }
            },
            villages: {
                "Nakasero I": ["Nakasero Village A", "Nakasero Village B", "Nakasero Village C"],
                "Kololo I": ["Kololo Village A", "Kololo Village B", "Kololo Village C"],
                "Kawempe I": ["Kawempe Village A", "Kawempe Village B", "Kawempe Village C"],
                "Makindye I": ["Makindye Village A", "Makindye Village B", "Makindye Village C"],
                "Nakawa I": ["Nakawa Village A", "Nakawa Village B", "Nakawa Village C"],
                "Rubaga I": ["Rubaga Village A", "Rubaga Village B", "Rubaga Village C"]
            }
        },
        "Wakiso": {
            counties: ["Busiro", "Kyadondo"],
            subcounties: {
                "Busiro": {
                    "Entebbe": ["Entebbe Central", "Kitoro", "Nakiwogo"],
                    "Katabi": ["Katabi Central", "Kitooro", "Abayita Ababiri"],
                    "Bussi": ["Bussi Central", "Bussi Islands"]
                },
                "Kyadondo": {
                    "Kira": ["Kira Town", "Bweyogerere", "Kireka"],
                    "Nangabo": ["Nangabo Central", "Kasangati", "Mpererwe"],
                    "Namayumba": ["Namayumba Central", "Kyanja", "Komamboga"]
                }
            },
            villages: {
                "Entebbe Central": ["Entebbe Village A", "Entebbe Village B", "Entebbe Village C"],
                "Kira Town": ["Kira Village A", "Kira Village B", "Kira Village C"],
                "Kasangati": ["Kasangati Village A", "Kasangati Village B", "Kasangati Village C"]
            }
        },
        "Mukono": {
            counties: ["Mukono", "Nakifuma"],
            subcounties: {
                "Mukono": {
                    "Mukono TC": ["Mukono Central", "Goma", "Seeta"],
                    "Goma": ["Goma Central", "Namugongo", "Kyampisi"],
                    "Nama": ["Nama Central", "Namaliga"]
                },
                "Nakifuma": {
                    "Nakifuma": ["Nakifuma Central", "Naggalama"],
                    "Kimenyedde": ["Kimenyedde Central", "Ngogwe"]
                }
            },
            villages: {
                "Mukono Central": ["Mukono Village A", "Mukono Village B", "Mukono Village C"],
                "Goma Central": ["Goma Village A", "Goma Village B", "Goma Village C"]
            }
        },
        "Jinja": {
            counties: ["Jinja Municipality", "Butembe"],
            subcounties: {
                "Jinja Municipality": {
                    "Central Division": ["Jinja Central", "Main Street", "Clive Road"],
                    "Walukuba": ["Walukuba East", "Walukuba West", "Masese"],
                    "Mpumudde": ["Mpumudde Central", "Budondo"]
                },
                "Butembe": {
                    "Butembe": ["Butembe Central", "Buyengo"],
                    "Buwenge": ["Buwenge Town", "Buwenge Rural"]
                }
            },
            villages: {
                "Jinja Central": ["Jinja Village A", "Jinja Village B", "Jinja Village C"],
                "Walukuba East": ["Walukuba Village A", "Walukuba Village B"]
            }
        },
        "Mbale": {
            counties: ["Mbale Municipality", "Bungokho"],
            subcounties: {
                "Mbale Municipality": {
                    "Industrial Division": ["Industrial Area", "Naboa", "Namakwekwe"],
                    "Northern Division": ["Northern Area", "Namabasa", "Bungokho"],
                    "Wanale": ["Wanale Central", "Bufumbo"]
                },
                "Bungokho": {
                    "Bungokho": ["Bungokho Central", "Bukhalu"],
                    "Buwalasi": ["Buwalasi Central", "Busulani"]
                }
            },
            villages: {
                "Industrial Area": ["Industrial Village A", "Industrial Village B"],
                "Bungokho Central": ["Bungokho Village A", "Bungokho Village B"]
            }
        },
        "Gulu": {
            counties: ["Gulu Municipality", "Aswa"],
            subcounties: {
                "Gulu Municipality": {
                    "Bardege Division": ["Bardege Central", "Layibi", "Pece"],
                    "Layibi Division": ["Layibi Central", "Laroo", "Pece Cwiny"],
                    "Pece Division": ["Pece Central", "Pece Aywee"]
                },
                "Aswa": {
                    "Aswa": ["Aswa Central", "Patiko"],
                    "Awach": ["Awach Central", "Palenga"],
                    "Bungatira": ["Bungatira Central", "Bobi"]
                }
            },
            villages: {
                "Bardege Central": ["Bardege Village A", "Bardege Village B"],
                "Aswa Central": ["Aswa Village A", "Aswa Village B"]
            }
        },
        "Mbarara": {
            counties: ["Mbarara Municipality", "Kashari"],
            subcounties: {
                "Mbarara Municipality": {
                    "Kamukuzi Division": ["Kamukuzi Central", "Ruharo", "Kakiika"],
                    "Nyamitanga Division": ["Nyamitanga Central", "Kizungu"],
                    "Kakoba Division": ["Kakoba Central", "Katete"]
                },
                "Kashari": {
                    "Kashari": ["Kashari Central", "Rubaya"],
                    "Rubindi": ["Rubindi Central", "Rwanyamahembe"],
                    "Nyakayojo": ["Nyakayojo Central", "Kagongi"]
                }
            },
            villages: {
                "Kamukuzi Central": ["Kamukuzi Village A", "Kamukuzi Village B"],
                "Kashari Central": ["Kashari Village A", "Kashari Village B"]
            }
        },
        "Masaka": {
            counties: ["Masaka Municipality", "Bukoto"],
            subcounties: {
                "Masaka Municipality": {
                    "Katwe Division": ["Katwe Central", "Nyendo", "Kimaanya"],
                    "Nyendo Division": ["Nyendo Central", "Kabonera"]
                },
                "Bukoto": {
                    "Bukoto": ["Bukoto Central", "Kyanamukaka"],
                    "Kyesiiga": ["Kyesiiga Central", "Butenga"]
                }
            },
            villages: {
                "Katwe Central": ["Katwe Village A", "Katwe Village B"],
                "Bukoto Central": ["Bukoto Village A", "Bukoto Village B"]
            }
        },
        "Lira": {
            counties: ["Lira Municipality", "Erute"],
            subcounties: {
                "Lira Municipality": {
                    "Adyel Division": ["Adyel Central", "Ojwina", "Ngetta"],
                    "Ojwina Division": ["Ojwina Central", "Boroboro"]
                },
                "Erute": {
                    "Erute": ["Erute Central", "Aromo"],
                    "Aromo": ["Aromo Central", "Aduku"]
                }
            },
            villages: {
                "Adyel Central": ["Adyel Village A", "Adyel Village B"],
                "Erute Central": ["Erute Village A", "Erute Village B"]
            }
        },
        "Arua": {
            counties: ["Arua Municipality", "Ayivu"],
            subcounties: {
                "Arua Municipality": {
                    "Arua Hill Division": ["Arua Hill Central", "Oli", "Pangisa"],
                    "River Oli Division": ["River Oli Central", "Ediofe"]
                },
                "Ayivu": {
                    "Ayivu": ["Ayivu Central", "Pajulu"],
                    "Adumi": ["Adumi Central", "Oluko"]
                }
            },
            villages: {
                "Arua Hill Central": ["Arua Village A", "Arua Village B"],
                "Ayivu Central": ["Ayivu Village A", "Ayivu Village B"]
            }
        }
    }
};
